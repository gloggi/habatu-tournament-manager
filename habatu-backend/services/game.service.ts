import { Category, Game, Hall, Option, Team, Timeslot } from "../models";
import { IGame } from '../interfaces/game.interface';
import { PopulatedDoc, Types } from "mongoose";
import { allocateSlots, gamesGenerator, generateTimeslots } from "../utils/roundRobin";
import { IHall } from "../interfaces/hall.interface";
import { ITeam } from "../interfaces/team.interface";
import { ICategory } from "../interfaces/category.interface";
import { IOption } from "../interfaces/option.interface";
import { ITimeslot } from "../interfaces/timeslot.interface";
import {format} from "date-fns"

export const createGame = async (game: IGame) => {
    return await Game.create(game)
}
export const getGame = async (_id: string) => {
    if (!Types.ObjectId.isValid(_id)) {
        throw new Error("")
    }
    const game = await Game.findOne({ _id });
    return game
}
export const getGames = async () => {
    const games = await Game.find({});
    return games
}

export const updateGames = async (_id: String, newGame: IGame) => {
    const game = await Game.findOneAndUpdate({ _id }, newGame, { new: true });
    return game
}

export const deleteGame = async (_id: String) => {
    await Game.deleteOne({ _id });
    return {}
}

export const getGamesPreview = async () => {
   
    const teams :ITeam[] = await Team.find({},{},).lean() as ITeam[]
    const categories : ICategory[] = await Category.find({}).lean();
    const halls: IHall[] = await Hall.find({}).lean({ autopopulate: true });
    const option :IOption = await Option.findOne().orFail().lean();
    const tempGames = gamesGenerator(teams, categories, halls, option)
     /*
    await Game.deleteMany({})
    await Timeslot.deleteMany({})
    const timeslots = await Timeslot.insertMany(generateTimeslots(tempGames,halls,option)) as ITimeslot[]
    const newGames = allocateSlots(tempGames,halls, timeslots)
    await Game.insertMany(newGames)
     */
    const timeslots = await Timeslot.find({})
    const games = await Game.find({}).populate<{hall: IHall}>("hall").populate<{timeslot: ITimeslot}>("timeslot")
    const output :any = {}
    for(let timeslot of timeslots){
        output[`${format(timeslot.startTime, "HH:mm")} - ${format(timeslot.endTime, "HH:mm")}|${timeslot._id}`] = {}
        for(let hall of halls){
            output[`${format(timeslot.startTime, "HH:mm")} - ${format(timeslot.endTime, "HH:mm")}|${timeslot._id}`][`${hall.name}|${hall._id}`] = games.filter(g=>g.timeslot!._id!.toString()==timeslot._id!.toString()&&g.hall!._id!.toString()==hall._id!.toString())
        }
    }

    
    return output
}