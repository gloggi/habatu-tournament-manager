import { gamesGenerator } from "./roundRobin.js"
import { ICategory } from "../interfaces/category.interface.js"
import { IHall } from "../interfaces/hall.interface.js"
import { IGame } from "../interfaces/game.interface.js"
import { ITeam } from "../interfaces/team.interface.js"
import { IOption } from "../interfaces/option.interface.js"
import { Document, HydratedDocument, Types, ObjectId } from 'mongoose';
import {Category, Team, Hall, Option} from "../models/index"
/*
const categories : ICategory[] = [
  {
    "_id": "62824fcc00d8c849809675d3",
    "name": "Gemischt",
    "color": "DarkSeaGreen"
  },
  {
    "_id": "62824fd200d8c849809675d5",
    "name": "Mädchen",
    "color": "pink"
  },
  {
    "_id": "62824fdb00d8c849809675d7",
    "name": "Leitende",
    "color": "aliceblue"
  }
]
const halls: IHall[] = [
  {
    "_id": "62824fb300d8c849809675cd",
    "name": "Halle A",
  },
  {
    "_id": "62824fb700d8c849809675cf",
    "name": "Halle B",
  },
  {
    "_id": "62824fbb00d8c849809675d1",
    "name": "Halle C",
  }
]
const teamNames: string[] = Array.from(new Set(["Fireballs", "Great Balls of Fire", "Dribble Down", "Down and Outfield", "Common Goal", "Hands Up", "Goal Getters", "Common Goal", "Goal Getters", "On the Attack", "Net Navigators", "Marauding Monkey Masters", "Dribble Down", "Wild Fish", "SOJA", "Rusty Trombones", "Ball Busters", "Delusional Duck Disses", "McDaniels", "On the Attack", "Doubling Down", "Valuable Possessions", "Nothing But Net", "Valuable Possessions", "Rampaging Rump Reckers", "Handball Hustlers", "Oh Shoot HandballersOh Shoot", "Handballers", "Get in Formation", "First Wave", "Handball Hustlers", "Goal-Oriented", "Fireballs", "Net Navigators", "Red Blue Jays", "Super Sonic Sandals", "Down and Outfield", "In Good Hands", "Nothing But Net", "Shot Through the Heart", "Court Crew", "Goal-Oriented", "First Wave", "Court Crew", "In Good Hands", "Ball Busters", "Great Balls of Fire", "Pass Posse", "Hands Up"]))
const option: IOption = {
  "tournamentName": "HaBaTu",
  "startTime": new Date("2022/09/04  10:00"),
  "gameDuration": new Date("2022/09/04  00:10"),
  "breakDuration": new Date("2022/09/04  00:05")
}
const teams: ITeam[] = []
for (let i = 0; i < 5; i++) {
  for (let category of categories) {
      teams.push({
          _id: teamNames[teamNames.length - 1],
          name: teamNames[teamNames.length - 1],
          category: category._id as string
      })
      teamNames.pop()
  }

}
const games: IGame[] = gamesGenerator(teams, categories, halls, option)

test("Checks round Robin works", () => {
    
    console.log(games.length)
    expect(games.length).toBeTruthy();
})

test("Each team plays enough games",()=>{
  let gameCounter = 0
  let team = teams[0]
  let gamesPlayed = teams.filter(t=>t.category==team.category).length-1
  for(const game of games){
    if(game.teamA==team._id||game.teamB==team._id){
      gameCounter++
    }
  }
  expect(gameCounter).toEqual(gamesPlayed)
})
test("No one plays against itself", ()=>{
  let againstItself = false
  for(const game of games){
    againstItself = againstItself ||game.teamA==game.teamB

  }
  expect(againstItself).toBeFalsy()
})
test("Team names are unique", ()=>{
  const teamsUniqueLength = new Set(teams.map(t=>t.name)).size
  const teamsLength = teams.length
  expect(teamsUniqueLength).toEqual(teamsLength)
})
test("Teams dont play at the same Time",()=>{
  for(const team of teams){
    const gamesByTeam : IGame[] = games.filter((g)=>{team._id==g.teamA||team._id==g.teamB})
    const times = new Set()
    for(const g of gamesByTeam){
      times.add(g.startTime)
    }
    expect(times.size).toEqual(gamesByTeam.length)
  }
})
*/