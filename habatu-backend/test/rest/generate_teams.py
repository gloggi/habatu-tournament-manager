import requests
token ="eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJfaWQiOiI2MzYyODRkZDEyZDhmZjkwNjMwMzhjNjMiLCJuaWNrbmFtZSI6ImFyY2hpIiwicm9sZSI6IkFkbWluIiwiaWF0IjoxNjY3NDAxMTA3LCJleHAiOjE2Njc0NDQzMDd9.Pi7yEtyGxZqF7Uw5IIEEvFaZRso9fO_e5uNJN8tBOig"
r = requests.get("https://habatu-backend.wildert.ch/categories")
categories = r.json()
r = requests.get("https://habatu-backend.wildert.ch/sections")
sections = r.json()

teamNames = {"Fireballs","Great Balls of Fire","Dribble Down","Down and Outfield","Common Goal","Hands Up","Goal Getters","Common Goal","Goal Getters","On the Attack","Net Navigators","Marauding Monkey Masters","Dribble Down","Wild Fish","SOJA","Rusty Trombones","Ball Busters","Delusional Duck Disses","McDaniels","On the Attack","Doubling Down","Valuable Possessions","Nothing But Net","Valuable Possessions","Rampaging Rump Reckers","Handball Hustlers","Oh Shoot HandballersOh Shoot","Handballers","Get in Formation","First Wave","Handball Hustlers","Goal-Oriented","Fireballs","Net Navigators","Red Blue Jays","Super Sonic Sandals","Down and Outfield","In Good Hands","Nothing But Net","On the Attack","Court Crew","Goal-Oriented","First Wave","Court Crew","In Good Hands","Ball Busters","Great Balls of Fire","Pass Posse","Hands Up"}
teamNames = list(teamNames)

teams = []
for (i, section) in enumerate(sections):
  for (j,category) in enumerate(categories):
    teams.append({"name": teamNames[i*3+j], "category": category["_id"], "section": section["_id"]})

for team in teams:
  r= requests.post("https://habatu-backend.wildert.ch/teams", json=team, headers={"Authorization": "Bearer eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJfaWQiOiI2MzYyODRkZDEyZDhmZjkwNjMwMzhjNjMiLCJuaWNrbmFtZSI6ImFyY2hpIiwicm9sZSI6IkFkbWluIiwiaWF0IjoxNjY3NDAxMTA3LCJleHAiOjE2Njc0NDQzMDd9.Pi7yEtyGxZqF7Uw5IIEEvFaZRso9fO_e5uNJN8tBOig"})
  print(r.text)
