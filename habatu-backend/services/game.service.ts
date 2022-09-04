import { Category, Game, Hall } from "../models";
import { IGame } from '../interfaces/game.interface';
import { Types } from "mongoose";
import { Team } from "../models";
import { gamesGenerator } from "../utils/roundRobin";

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
    const teams = await Team.find({},{},{autopopulate: false})
    const categories = await Category.find({});
    const halls = await Hall.find({});
    const games = gamesGenerator(teams, categories, halls)
    return games
}