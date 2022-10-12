import { Category, Game, Hall, Option, Team, Timeslot } from "../models";
import { allocateSlots, gamesGenerator, generateTimeslots } from "../utils/roundRobin";
import { IHall } from "../interfaces/hall.interface";
import { ITeam } from "../interfaces/team.interface";
import { ICategory } from "../interfaces/category.interface";
import { IOption } from "../interfaces/option.interface";
import { ITimeslot } from "../interfaces/timeslot.interface";
import {format} from "date-fns"

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
    const ts = await Timeslot.find({})
    const games = await Game.find({}).populate<{hall: IHall}>("hall").populate<{timeslot: ITimeslot}>("timeslot")
    const output :any = {}
    for(let timeslot of ts){
        output[`${format(timeslot.startTime, "HH:mm")} - ${format(timeslot.endTime, "HH:mm")}`] = {}
        output[`${format(timeslot.startTime, "HH:mm")} - ${format(timeslot.endTime, "HH:mm")}`]["id"] = timeslot._id
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