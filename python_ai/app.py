from __future__ import annotations

import os
from typing import Any

from dotenv import load_dotenv
from flask import Flask, jsonify, request
from waitress import serve

from matcher import find_top_matches

load_dotenv()

app = Flask(__name__)
app.config["MAX_CONTENT_LENGTH"] = 35 * 1024 * 1024
API_KEY = os.getenv("MATCH_API_KEY", "change-this-local-key")
MAX_CANDIDATES = int(os.getenv("MAX_CANDIDATES", "100"))


def authorised() -> bool:
    supplied = request.headers.get("X-FindIt-Key", "")
    return bool(API_KEY) and supplied == API_KEY


@app.get("/health")
def health() -> Any:
    return jsonify({"status": "ok", "service": "FindIt AI matcher"})


@app.post("/match")
def match() -> Any:
    if not authorised():
        return jsonify({"error": "unauthorised"}), 401
    payload = request.get_json(silent=True)
    if not isinstance(payload, dict):
        return jsonify({"error": "JSON request body is required"}), 400
    source = payload.get("source")
    candidates = payload.get("candidates")
    if not isinstance(source, dict) or not isinstance(candidates, list):
        return jsonify({"error": "source and candidates are required"}), 400
    candidates = candidates[:MAX_CANDIDATES]
    try:
        matches = find_top_matches(source, candidates, limit=5)
        return jsonify({"matches": matches, "method": "weighted-image-text-attribute-similarity"})
    except Exception as exc:
        app.logger.exception("Matching failed")
        return jsonify({"error": "matching_failed", "details": str(exc)}), 500


if __name__ == "__main__":
    host = os.getenv("HOST", "127.0.0.1")
    port = int(os.getenv("PORT", "5000"))
    if os.getenv("PRODUCTION", "0") == "1":
        serve(app, host=host, port=port, threads=4)
    else:
        app.run(host=host, port=port, debug=True)
