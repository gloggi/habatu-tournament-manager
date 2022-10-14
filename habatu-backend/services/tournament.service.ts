import { Category, Game, Hall, Option, Team, Timeslot } from "../models";
import { allocateSlots, gamesGenerator, generateTimeslots } from "../utils/roundRobin";
import { IHall } from "../interfaces/hall.interface";
import { ITeam } from "../interfaces/team.interface";
import { ICategory } from "../interfaces/category.interface";
import { IOption } from "../interfaces/option.interface";
import { ITimeslot } from "../interfaces/timeslot.interface";
import {format, isWithinInterval, addMinutes, getMinutes} from "date-fns"

export const getGamesPreview = async () => {
   
    const teams :ITeam[] = await Team.find({},{},).lean() as ITeam[]
    const categories : ICategory[] = await Category.find({}).lean();
    const halls: IHall[] = await Hall.find({}).lean({ autopopulate: true });
    const option :IOption = await Option.findOne().orFail().lean();
    const tempGames = gamesGenerator(teams, categories, halls, option)
     
    await Game.deleteMany({})
    await Timeslot.deleteMany({})
    const timeslots = await Timeslot.insertMany(generateTimeslots(tempGames,halls,option)) as ITimeslot[]
    const newGames = allocateSlots(tempGames,halls, timeslots)
    await Game.insertMany(newGames)
   

    
    return await getTournamentTable()
}

export const getTournamentTable = async () => {
    const halls: IHall[] = await Hall.find({}).lean({ autopopulate: true });
    const option :IOption = await Option.findOne().orFail().lean();
    const ts = await Timeslot.find({})
    const games = await Game.find({}).populate<{hall: IHall}>("hall").populate<{timeslot: ITimeslot}>("timeslot")
    const output :any = {}
    console.log(ts[0].startTime)
    console.log(new Date())
    for(let timeslot of ts){
        output[`${format(timeslot.startTime, "HH:mm")} - ${format(timeslot.endTime, "HH:mm")}`] = {}
        output[`${format(timeslot.startTime, "HH:mm")} - ${format(timeslot.endTime, "HH:mm")}`]["id"] = timeslot._id
        output[`${format(timeslot.startTime, "HH:mm")} - ${format(timeslot.endTime, "HH:mm")}`]["isNow"] = isWithinInterval(new Date(), {start:timeslot.startTime, end: addMinutes(timeslot.endTime, parseInt(option.breakDuration.split(":")[1])) })
        output[`${format(timeslot.startTime, "HH:mm")} - ${format(timeslot.endTime, "HH:mm")}`]["items"] = {}
        for(let hall of halls){
            output[`${format(timeslot.startTime, "HH:mm")} - ${format(timeslot.endTime, "HH:mm")}`]["items"][`${hall.name}`] = {id: hall._id, items: games.filter(g=>g.timeslot!._id!.toString()==timeslot._id!.toString()&&g.hall!._id!.toString()==hall._id!.toString())}
        }
    }
    return output
}

export const getTimePreview= async () => {
    await getGamesPreview()
    const teams :ITeam[] = await Team.find({},{},).lean() as ITeam[]
    const categories : ICategory[] = await Category.find({}).lean();
    const halls: IHall[] = await Hall.find({}).lean({ autopopulate: true });
    const option :IOption = await Option.findOne().orFail().lean();
    const games = await Game.find({}).populate<{hall: IHall}>("hall").populate<{timeslot: ITimeslot}>("timeslot")
   
    return {
        amountOfGames: games.length,
        lastGame: games[games.length-1].timeslot.endTime
    }

}

export const getRanking = async () => {
    await getGamesPreview()
    const teams :ITeam[] = await Team.find({},{},).lean()
    const categories : ICategory[] = await Category.find({}).lean();
    const halls: IHall[] = await Hall.find({}).lean({ autopopulate: true });
    const option :IOption = await Option.findOne().orFail().lean();
    const games = await Game.find({})
    const rankedTeams : any = [...teams]
    for(let team of rankedTeams){
        var tournamentPoints = 0
        var pointsPro = 0
        var pointsCon = 0
        for(let game of games){
            if(game.teamA== team.id){
                pointsPro+= game.pointsTeamA
                pointsCon+= game.pointsTeamB
                if(game.pointsTeamA>game.pointsTeamB){
                    tournamentPoints+=2
                }if(game.pointsTeamA==game.pointsTeamB){
                    tournamentPoints+=1
                }
            }
            if(game.teamB== team.id){
                pointsPro+= game.pointsTeamB
                pointsCon+= game.pointsTeamA
                if(game.pointsTeamA<game.pointsTeamB){
                    tournamentPoints+=2
                }if(game.pointsTeamA==game.pointsTeamB){
                    tournamentPoints+=1
                }
            }
        }
        team.tournamentPoints = tournamentPoints
        team.pointsPro = pointsPro
        team.pointsCon = pointsCon
        
    }

    return rankedTeams

}