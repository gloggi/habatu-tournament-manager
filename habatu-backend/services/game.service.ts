import { Category, Game, Hall, Option, Team } from "../models";
import { IGame } from '../interfaces/game.interface';
import { PopulatedDoc, Types } from "mongoose";
import { gamesGenerator } from "../utils/roundRobin";
import { IHall } from "../interfaces/hall.interface";
import { ITeam } from "../interfaces/team.interface";
import { ICategory } from "../interfaces/category.interface";
import { IOption } from "../interfaces/option.interface";

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
    const options :IOption = await Option.findOne().orFail().lean();
    const tempGames = gamesGenerator(teams, categories, halls, options)
    await Game.deleteMany({})
    await  Game.insertMany(tempGames)
    const games = await Game.find({}).populate<{ hall: IHall }>('hall')
    
    const table: Record<string, Record<string, any[]>> = {}
    for(let game of games){
        if(table[game.startTime.toString()]==undefined){
            table[game.startTime.toString()] ={}
        }
        if(table[game.startTime.toString()][game.hall.name]==undefined){
            table[game.startTime.toString()][game.hall.name] = []
        }
        table[game.startTime.toString()][game.hall.name].push(game)
    }
    return {table, halls}
}