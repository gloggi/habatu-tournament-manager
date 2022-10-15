import { ITeam } from "../interfaces/team.interface"
import { IGame } from "../interfaces/game.interface"
import { ICategory } from "../interfaces/category.interface"
import { IHall } from "../interfaces/hall.interface"
import { IOption } from "../interfaces/option.interface"
import { Document, HydratedDocument, Types } from 'mongoose';
import { flattenDeep, zip } from "lodash"
import { setTimeOnDate } from "./helper"
import { addMinutes, getMinutes } from "date-fns"
import { ITimeslot } from "../interfaces/timeslot.interface"

export const gamesGenerator = (teams: ITeam[], categories: ICategory[], halls: IHall[], option: IOption): IGame[] => {
    const teamsByCategory: Record<string, ITeam[]> = {}
    for (let category of categories) {
        teamsByCategory[category._id.toString()] = teams.filter((team) => team.category == category._id.toString())
    }
    const gamesByCategory: Record<string, IGame[]> = {}
    for (let category in teamsByCategory) {
        gamesByCategory[category] = []
        const cTeams :ITeam[] = teamsByCategory[category]

        var roundTeams : ITeam[] = []
        if (!(cTeams.length % 2)) {
            roundTeams = cTeams.slice(0, cTeams.length - 1)
        } else {
            roundTeams = cTeams
        }
        for (let i = 0; i < roundTeams.length; i++) {
            const pivotTeam :ITeam = roundTeams[i]
            if (!(cTeams.length % 2)) {
                var endTeam : ITeam = cTeams[cTeams.length - 1]
                gamesByCategory[category].push({
                    teamA: pivotTeam._id.toString(),
                    teamB: endTeam._id.toString(),
                    category: category,
                    pointsTeamA: 0,
                    pointsTeamB: 0
                })
            }
            for (let j = 1; j < cTeams.length / 2; j++) {
                const teamA = roundTeams[(i + j) % roundTeams.length]
                const teamB = roundTeams[(i - j + roundTeams.length) % roundTeams.length]
                gamesByCategory[category].push({
                    teamA: teamA._id.toString(),
                    teamB: teamB._id.toString(),
                    category: category,
                    pointsTeamA: 0,
                    pointsTeamB: 0
                })
            }

        }



    }
    const tempGames: Array<IGame[]> = []
    for (let category in teamsByCategory) {
        tempGames.push(gamesByCategory[category])
    }
    const games: IGame[] = flattenDeep<Array<IGame>>(zip<IGame>(...tempGames) as IGame[][]).filter(g => g)


    return games
}
export const generateTimeslots = (games: IGame[], halls: IHall[], option: IOption): ITimeslot[] => {
    const nslots = Math.ceil(games.length / halls.length) + 3
    const timeSlots: ITimeslot[] = []
    let startTime = setTimeOnDate(new Date(), option.startTime)
    console.log(startTime)
    for (let i = 0; i < nslots; i++) {
        timeSlots.push({
            startTime: startTime,
            endTime: addMinutes(startTime, parseInt(option.gameDuration.split(":")[1])),
        })
        startTime = addMinutes(startTime, parseInt(option.gameDuration.split(":")[1]) + parseInt(option.breakDuration.split(":")[1]))
    }
    return timeSlots
}

export const allocateSlots = (games: IGame[], halls: IHall[], timeslots: ITimeslot[]): IGame[] => {
    let slotCounter = 0
    for (const [i, game] of games.entries()) {
        const hallIndex = i % halls.length
        game.hall = halls[hallIndex]._id.toString()
        game.timeslot = timeslots[slotCounter]._id as string;
        if (hallIndex == halls.length - 1) {
            slotCounter++
        }
    }
    return games
}