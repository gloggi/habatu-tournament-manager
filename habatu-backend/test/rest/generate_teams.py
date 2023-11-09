import requests
import random
url ="https://habatu.gloggi.ch/api"
# url = "http://localhost:8000"
# Server details and token
token = "eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJfaWQiOiI2NTRjYjFiNzE5ZjAwOTgyNzBiMGFkY2UiLCJuaWNrbmFtZSI6ImFyY2hpIiwicm9sZSI6IkFkbWluIiwiaWF0IjoxNjk5NTI1MTA1LCJleHAiOjE2OTk1NjgzMDV9.h-apO1nfQH8BpHcYEyKrcaXR0Qka7wvVtnM9pUb-NWg"
headers = {"Authorization": f"Bearer {token}"}

# Delete Teams
teams = requests.get(f"{url}/timeslots", headers=headers).json()
for team in teams:
    requests.delete(f"{url}/timeslots/{team['_id']}", headers=headers)
"""
# Fetch categories and sections
categories_response = requests.get(f"{url}/categories").json()
sections_response = requests.get(f"{url}/sections").json()

# Convert responses to dictionaries for easy access
categories = {category['name']: category['_id'] for category in categories_response}
sections = {section['name']: section['_id'] for section in sections_response}

# Teams structure
teams_structure = {
    "Wildert": {"Gemischt": 2, "Mädchen": 1, "Leitende": 1},
    "Gryfensee": {"Gemischt": 1, "Mädchen": 1, "Leitende": 1},
    "Hadlaub": {"Gemischt": 1, "Mädchen": 1, "Leitende": 1},
    "Lägern": {"Gemischt": 1, "Mädchen": 0, "Leitende": 1},
    "Manegg": {"Gemischt": 0, "Mädchen": 2, "Leitende": 1},
    "See": {"Gemischt": 2, "Mädchen": 1, "Leitende": 1},
    "Gloggi": {"Gemischt": 0, "Mädchen": 0,"Leitende": 1},
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
"""