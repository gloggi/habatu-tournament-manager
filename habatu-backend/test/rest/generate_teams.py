import requests
categories = [
  {
    "_id": "62824596b7de7a472c9d7343",
    "name": "Gemischt",
    "__v": 0
  },
  {
    "_id": "62824fcc00d8c849809675d3",
    "name": "Gemischt",
    "__v": 0
  },
  {
    "_id": "62824fd200d8c849809675d5",
    "name": "Mädchen",
    "__v": 0
  },
  {
    "_id": "62824fdb00d8c849809675d7",
    "name": "Leitende",
    "__v": 0
  }
]
sections = [
  {
    "_id": "628245341ac271b77a898826",
    "name": "Wildert",
    "__v": 0
  },
  {
    "_id": "62824fea00d8c849809675d9",
    "name": "Wildert",
    "__v": 0
  },
  {
    "_id": "62824ff700d8c849809675db",
    "name": "Gryfensee",
    "__v": 0
  },
  {
    "_id": "62824ffd00d8c849809675dd",
    "name": "Hadlaub",
    "__v": 0
  },
  {
    "_id": "6282500600d8c849809675df",
    "name": "Lägern",
    "__v": 0
  },
  {
    "_id": "6282500a00d8c849809675e1",
    "name": "Manegg",
    "__v": 0
  },
  {
    "_id": "6282501100d8c849809675e3",
    "name": "See",
    "__v": 0
  }
]
teamNames = ["Fireballs","Great Balls of Fire","Dribble Down","Down and Outfield","Common Goal","Hands Up","Goal Getters","Common Goal","Goal Getters","On the Attack","Net Navigators","Marauding Monkey Masters","Dribble Down","Wild Fish","SOJA","Rusty Trombones","Ball Busters","Delusional Duck Disses","McDaniels","On the Attack","Doubling Down","Valuable Possessions","Nothing But Net","Valuable Possessions","Rampaging Rump Reckers","Handball Hustlers","Oh Shoot HandballersOh Shoot","Handballers","Get in Formation","First Wave","Handball Hustlers","Goal-Oriented","Fireballs","Net Navigators","Red Blue Jays","Super Sonic Sandals","Down and Outfield","In Good Hands","Nothing But Net","Shot Through the Heart","Court Crew","Goal-Oriented","First Wave","Court Crew","In Good Hands","Ball Busters","Great Balls of Fire","Pass Posse","Hands Up"]
teams = []
for (i, section) in enumerate(sections):
  for (j,category) in enumerate(categories):
    teams.append({"name": teamNames[i*3+j], "category": category["_id"], "section": section["_id"]})

print(teams)
for team in teams:
  r= requests.post("http://localhost:8000/teams", json=team)
  print(r.text)