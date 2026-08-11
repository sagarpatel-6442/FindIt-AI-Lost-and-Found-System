import sys
from pathlib import Path
sys.path.insert(0, str(Path(__file__).resolve().parents[2] / 'python_ai'))
from matcher import find_top_matches


def test_basic_ranking():
    source = {"id": 1, "description": "black nike backpack", "colour": "black", "category": "Bag", "location": "library", "incident_date": "2026-08-04", "image": None}
    candidates = [
        {"id": 2, "description": "black backpack with nike logo", "colour": "black", "category": "Bag", "location": "library entrance", "incident_date": "2026-08-05", "image": None},
        {"id": 3, "description": "silver water bottle", "colour": "silver", "category": "Bottle", "location": "car park", "incident_date": "2026-07-01", "image": None},
    ]
    matches = find_top_matches(source, candidates)
    assert matches[0]["item_id"] == 2


if __name__ == "__main__":
    test_basic_ranking()
    print("Matcher test passed")
