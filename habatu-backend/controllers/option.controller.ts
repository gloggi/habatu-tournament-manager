import { Request, Response } from 'express';
import { createOption as create, getOptions as gets, updateOptions as update, getOption as get, deleteOption as remove } from "../services/option.service";

export const createOption = async (req: Request, res: Response) => {
    const option = await create(req.body)
    res.json(option)
}
export const getOption = async (req: Request, res: Response) => {
    const option = await get(req.params.id)
    res.json(option)
}
export const getOptions = async (req: Request, res: Response) => {
    const options = await gets()
    res.json(options)
}
export const updateOption = async (req: Request, res: Response) => {
    const option = await update(req.params.id, req.body)
    res.json(option)
}
export const deleteOption = async (req: Request, res: Response) => {
    const option = await remove(req.params.id)
    res.json(option)
}
