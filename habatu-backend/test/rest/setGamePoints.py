import requests
import json
import random

r = requests.get("http://localhost:8000/games")
games = r.json()
game_ids = [g["_id"] for g in games]
print(games[0])
for id in game_ids:
    update = {
        "pointsTeamA": random.randint(0,20),
        "pointsTeamB": random.randint(0,20)
    }
    rt = requests.put("http://localhost:8000/games/%s"%id, json=update)
    print(rt.text)
