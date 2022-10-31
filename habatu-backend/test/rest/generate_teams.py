import requests
categories = [
  {
    "_id": "635fa1a7fb4a34ffc8694192",
    "name": "Gemischt",
    "__v": 0,
    "color": "#9f85ff"
  },
  {
    "_id": "635fa1abfb4a34ffc8694195",
    "name": "Mädchen",
    "__v": 0,
    "color": "#fb9d9d"
  },
  {
    "_id": "635fa1aefb4a34ffc8694198",
    "name": "Leitende",
    "__v": 0,
    "color": "#8fffc5"
  }
]
sections = [
  {
    "_id": "635fa187fb4a34ffc869417d",
    "name": "Wildert",
    "__v": 0
  },
  {
    "_id": "635fa18bfb4a34ffc8694180",
    "name": "Gryfensee",
    "__v": 0
  },
  {
    "_id": "635fa18ffb4a34ffc8694183",
    "name": "Hadlaub",
    "__v": 0
  },
  {
    "_id": "635fa195fb4a34ffc8694186",
    "name": "Lägern",
    "__v": 0
  },
  {
    "_id": "635fa197fb4a34ffc8694189",
    "name": "Manegg",
    "__v": 0
  },
  {
    "_id": "635fa19afb4a34ffc869418c",
    "name": "See",
    "__v": 0
  },
  {
    "_id": "635fa1a3fb4a34ffc869418f",
    "name": "Gloggi",
    "__v": 0
  }
]
teamNames = {"Fireballs","Great Balls of Fire","Dribble Down","Down and Outfield","Common Goal","Hands Up","Goal Getters","Common Goal","Goal Getters","On the Attack","Net Navigators","Marauding Monkey Masters","Dribble Down","Wild Fish","SOJA","Rusty Trombones","Ball Busters","Delusional Duck Disses","McDaniels","On the Attack","Doubling Down","Valuable Possessions","Nothing But Net","Valuable Possessions","Rampaging Rump Reckers","Handball Hustlers","Oh Shoot HandballersOh Shoot","Handballers","Get in Formation","First Wave","Handball Hustlers","Goal-Oriented","Fireballs","Net Navigators","Red Blue Jays","Super Sonic Sandals","Down and Outfield","In Good Hands","Nothing But Net","On the Attack","Court Crew","Goal-Oriented","First Wave","Court Crew","In Good Hands","Ball Busters","Great Balls of Fire","Pass Posse","Hands Up"}
teamNames = list(teamNames)
print(len(teamNames))

teams = []
for (i, section) in enumerate(sections):
  for (j,category) in enumerate(categories):
    teams.append({"name": teamNames[i*3+j], "category": category["_id"], "section": section["_id"]})

print(teams)

for team in teams:
  r= requests.post("http://localhost:8000/teams", json=team, headers={"Authorization": "Bearer eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJfaWQiOiI2MzVmYTE2MjI1MTgzNzY0M2M5MGFjNTIiLCJuaWNrbmFtZSI6ImFkbWluIiwicm9sZSI6IkFkbWluIiwiaWF0IjoxNjY3MjExNjM5LCJleHAiOjE2NjcyNTQ4Mzl9.24c-skDFBX6h6jnQB0bRZVqBGUxTbs-ynekXMdSM7Zg"})
  print(r.text)
