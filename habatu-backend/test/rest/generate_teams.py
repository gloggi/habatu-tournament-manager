import requests
import random
#url ="https://habatu.gloggi.ch/api"
url = "http://localhost:8000"
# Server details and token
token = "eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJfaWQiOiI2NzJiNzJkZGE2Y2FlODU0YzE3NWI1OTciLCJuaWNrbmFtZSI6ImFyY2hpIiwicm9sZSI6IkFkbWluIiwiaWF0IjoxNzMwOTkzMzM2LCJleHAiOjE3MzEwMzY1MzZ9.uGj9j01wPAZG5w_j6ZNYiFd3iL3wsYMsMEZbwNdfWlc"
headers = {"Authorization": f"Bearer {token}"}

# Delete Teams
teams = requests.get(f"{url}/timeslots", headers=headers).json()
for team in teams:
    requests.delete(f"{url}/timeslots/{team['_id']}", headers=headers)
games = requests.get(f"{url}/games", headers=headers).json()
for game in games:
    requests.delete(f"{url}/games/{game['_id']}", headers=headers)
# Fetch categories and sections
categories_response = requests.get(f"{url}/categories").json()
sections_response = requests.get(f"{url}/sections").json()

# Convert responses to dictionaries for easy access
categories = {category['name']: category['_id'] for category in categories_response}
sections = {section['name']: section['_id'] for section in sections_response}

# Teams structure
teams_structure = {
    "Wildert": {"Pfadis": 2,  "Leitende": 1},
    "Gryfensee": {"Pfadis": 1,  "Leitende": 1},
    "Hadlaub": {"Pfadis": 1,  "Leitende": 1},
    "Lägern": {"Pfadis": 1, "Leitende": 1},
    "Manegg": {"Pfadis": 2, "Leitende": 1},
    "See": {"Pfadis": 2,  "Leitende": 1},
}

# Generate a list of random team names
team_names = funny_team_names = [ "The Meme Machines", "Puns of Anarchy", "The Laughing Llamas", "Hilarious Hippos", "Giggles Galore", "Jokers' Wild", "The Chuckle Champions", "Silly Squad", "Funny Felines", "The Guffaw Gang", "Witty Whales", "Banter Bandits", "The Humor Hub", "Prankster Pack", "Comic Crusaders", "Snicker Seekers", "Giggle Gang", "Jest Quest", "Mirth Makers", "Wit Warriors", "Chortle Crew", "Yuk Yuk Yahoos", "Amusing Avengers", "Quip Queens", "Laughter Legion", "The Pun Pals", "Smirk Squad", "Jolly Jesters", "Chuckleheads", "Grin Guild" ]
random.shuffle(team_names)

# Create and post teams
for section_name, category_mapping in teams_structure.items():
    for category_name, team_count in category_mapping.items():
        for _ in range(team_count):
            team_name = team_names.pop()
            team = {
                "name": team_name,
                "category": categories[category_name],
                "section": sections[section_name]
            }
            # Post the team to the server
            requests.post(f"{url}/teams", json=team, headers=headers)

print("Teams created and posted successfully.")