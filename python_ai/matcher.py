from __future__ import annotations

import base64
import io
import math
import re
from datetime import date, datetime
from difflib import SequenceMatcher
from typing import Any

try:
    import imagehash
    from PIL import Image
except Exception:  # Image matching remains optional until requirements are installed.
    imagehash = None
    Image = None

try:
    from sklearn.feature_extraction.text import TfidfVectorizer
    from sklearn.metrics.pairwise import cosine_similarity
except Exception:  # pragma: no cover
    TfidfVectorizer = None
    cosine_similarity = None

WEIGHTS = {
    "image": 0.40,
    "description": 0.20,
    "category": 0.15,
    "colour": 0.10,
    "location": 0.10,
    "date": 0.05,
}


def clean_text(value: Any) -> str:
    text = str(value or "").lower().strip()
    return re.sub(r"\s+", " ", re.sub(r"[^\w\s-]", " ", text))


def text_similarity(a: Any, b: Any) -> float:
    a_text, b_text = clean_text(a), clean_text(b)
    if not a_text or not b_text:
        return 0.0
    if TfidfVectorizer and cosine_similarity:
        try:
            matrix = TfidfVectorizer(ngram_range=(1, 2), stop_words="english").fit_transform([a_text, b_text])
            return float(cosine_similarity(matrix[0:1], matrix[1:2])[0][0])
        except ValueError:
            pass
    return SequenceMatcher(None, a_text, b_text).ratio()


def exact_or_text(a: Any, b: Any) -> float:
    a_text, b_text = clean_text(a), clean_text(b)
    if not a_text or not b_text:
        return 0.0
    if a_text == b_text:
        return 1.0
    return text_similarity(a_text, b_text)


def decode_image(data_url: Any) -> Any:
    if imagehash is None or Image is None:
        return None
    if not isinstance(data_url, str) or not data_url.startswith("data:image/"):
        return None
    try:
        _, encoded = data_url.split(",", 1)
        raw = base64.b64decode(encoded, validate=True)
        if len(raw) > 6 * 1024 * 1024:
            return None
        image = Image.open(io.BytesIO(raw))
        image.verify()
        image = Image.open(io.BytesIO(raw)).convert("RGB")
        image.thumbnail((1024, 1024))
        return image
    except Exception:
        return None


def image_similarity(a_data: Any, b_data: Any) -> float:
    a_image, b_image = decode_image(a_data), decode_image(b_data)
    if a_image is None or b_image is None:
        return 0.0
    try:
        a_hash = imagehash.phash(a_image)
        b_hash = imagehash.phash(b_image)
        distance = a_hash - b_hash
        return max(0.0, 1.0 - distance / len(a_hash.hash.flatten()))
    finally:
        a_image.close()
        b_image.close()


def date_similarity(a: Any, b: Any) -> float:
    def parse(value: Any) -> date | None:
        try:
            return datetime.strptime(str(value), "%Y-%m-%d").date()
        except (TypeError, ValueError):
            return None

    first, second = parse(a), parse(b)
    if not first or not second:
        return 0.0
    days = abs((first - second).days)
    return max(0.0, 1.0 - min(days, 30) / 30.0)


def score_pair(source: dict[str, Any], candidate: dict[str, Any]) -> dict[str, Any]:
    components = {
        "image": image_similarity(source.get("image"), candidate.get("image")),
        "description": text_similarity(source.get("description"), candidate.get("description")),
        "category": exact_or_text(source.get("category"), candidate.get("category")),
        "colour": exact_or_text(source.get("colour"), candidate.get("colour")),
        "location": text_similarity(source.get("location"), candidate.get("location")),
        "date": date_similarity(source.get("incident_date"), candidate.get("incident_date")),
    }
    score = sum(components[name] * weight for name, weight in WEIGHTS.items())
    return {
        "item_id": int(candidate["id"]),
        "score": round(score * 100, 2),
        "components": {name: round(value * 100, 2) for name, value in components.items()},
    }


def find_top_matches(source: dict[str, Any], candidates: list[dict[str, Any]], limit: int = 5) -> list[dict[str, Any]]:
    scored = [score_pair(source, candidate) for candidate in candidates if candidate.get("id") is not None]
    scored.sort(key=lambda item: item["score"], reverse=True)
    return scored[: max(1, min(limit, 5))]
