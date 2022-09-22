import { ITeam } from "../interfaces/team.interface"
import { IGame } from "../interfaces/game.interface"
import { ICategory } from "../interfaces/category.interface"
import { IHall } from "../interfaces/hall.interface"
import { IOption } from "../interfaces/option.interface"
import { Document, HydratedDocument, Types } from 'mongoose';
import { flattenDeep, zip } from "lodash"
import {addMinutes, getMinutes} from "date-fns"

export const gamesGenerator = (teams: ITeam[], categories: ICategory[], halls: IHall[], option: IOption): IGame[] => {
    const teamsByCategory: Record<string, ITeam[]> = {}
    for (let category of categories) {
        teamsByCategory[category._id.toString()] = teams.filter((team) => team.category == category._id.toString())
    }
    console.log("Option: ", option.breakDuration.getMinutes())
    const gamesByCategory: Record<string, IGame[]> = {}
    for (let category in teamsByCategory) {
        gamesByCategory[category] =[]
        const teams = teamsByCategory[category]
        const teamsById: string[] = teams.map(team=>team._id.toString())
        const DUMMY: string = ""
        if(teamsById.length%2===1){
            teamsById.push(DUMMY)
        }
        for(let j=0;j<teamsById.length-1;j++){
            
            for(let i=0; i<teamsById.length/2;i++){
                const o = teamsById.length - 1 - i;
                if(teamsById[i]!==DUMMY&&teamsById[o]!==DUMMY){
                    const isHome = i === 0 && j % 2 === 1;
                    const game: IGame ={
                        teamA: isHome? teamsById[i] : teamsById[o],
                        teamB: isHome? teamsById[o] : teamsById[i],
                        category: category,
                        hall: "",
                        startTime: new Date(),
                        endTime: new Date(),
                        pointsTeamA: 0,
                        pointsTeamB: 0
                    }
                    gamesByCategory[category].push(game)

                }
                teamsById.splice(1, 0, teamsById.pop()!);

            }
        }

    }
    const tempGames: Array<IGame[]> = []
    for(let category in teamsByCategory){
        tempGames.push(gamesByCategory[category])
    }
    const games: IGame[]  = flattenDeep<Array<IGame>>(zip<IGame>(...tempGames) as IGame[][]).filter(g=>g)
    console.log(games)
    let startTime = option.startTime
    for (const [i, game] of games.entries()) {
        const hallIndex = i%halls.length
        game.startTime = startTime;
        game.endTime = addMinutes(startTime,getMinutes(option.gameDuration));
        game.hall = halls[hallIndex]._id.toString()
        
        if(hallIndex==halls.length-1){
            startTime = addMinutes(startTime, getMinutes(option.gameDuration)+getMinutes(option.breakDuration))
        }


    }

    return games
} 

const roundRobin = (teams:string[])=>{
    const DUMMY = ""
    const rounds = []
    if(teams.length%2==1){
        teams.push(DUMMY)
    }
    for(let i = 0;i<teams.length-1;i++){
        const o = teams.length - 1 - i;
        for(let j=0;j<teams.length/2;j++){
            if(teams[i]!==""||teams[o]!==""){

            }

        }
    }
}