import { ITeam } from "../interfaces/team.interface"
import { IGame } from "../interfaces/game.interface"
import { ICategory } from "../interfaces/category.interface"
import { IHall } from "../interfaces/hall.interface"
import { IOption } from "../interfaces/option.interface"
import { Document, HydratedDocument, Types } from 'mongoose';
import { flattenDeep, zip } from "lodash"
import { setTimeOnDate } from "./helper"
import {addMinutes, getMinutes} from "date-fns"
import { ITimeslot } from "../interfaces/timeslot.interface"

export const gamesGenerator = (teams: ITeam[], categories: ICategory[], halls: IHall[], option: IOption): IGame[] => {
    const teamsByCategory: Record<string, ITeam[]> = {}
    for (let category of categories) {
        teamsByCategory[category._id.toString()] = teams.filter((team) => team.category == category._id.toString())
    }
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


    return games
} 
export const generateTimeslots = (games: IGame[], halls: IHall[], option: IOption) :ITimeslot[]=>{
    const nslots = Math.ceil(games.length/halls.length)+3
    const timeSlots : ITimeslot[] = []
    let startTime = setTimeOnDate(new Date(),option.startTime)
    console.log(startTime)
    for(let i=0;i<nslots;i++){
        timeSlots.push({
            startTime: startTime,
            endTime: addMinutes(startTime,parseInt(option.gameDuration.split(":")[1])),
        })
        startTime = addMinutes(startTime, parseInt(option.gameDuration.split(":")[1])+parseInt(option.breakDuration.split(":")[1]))
    }
    return timeSlots
}

export const allocateSlots = (games: IGame[], halls: IHall[], timeslots: ITimeslot[]) : IGame[] =>{
    let slotCounter = 0
    for (const [i, game] of games.entries()) {
        const hallIndex = i%halls.length
        game.hall = halls[hallIndex]._id.toString()
        game.timeslot= timeslots[slotCounter]._id as string;
        if(hallIndex==halls.length-1){
            slotCounter++
        }
    }
    return games
}