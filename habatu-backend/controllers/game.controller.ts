import { Request, Response } from 'express';
import { createGame as create, getGames as gets, updateGames as update, getGame as get, deleteGame as remove } from "../services/game.service";

export const createGame = async (req: Request, res: Response) => {
    const game = await create(req.body)
    res.json(game)
}
export const getGame = async (req: Request, res: Response) => {
    const game = await get(req.params.id)
    res.json(game)
}
export const getGames = async (req: Request, res: Response) => {
    const games = await gets()
    res.json(games)
}
export const updateGame = async (req: Request, res: Response) => {
    const game = await update(req.params.id, req.body)
    res.json(game)
}
export const deleteGame = async (req: Request, res: Response) => {
    const game = await remove(req.params.id)
    res.json(game)
}

