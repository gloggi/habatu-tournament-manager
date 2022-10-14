import { Option } from "../models";
import { IOption } from '../interfaces/option.interface';
import { Types } from "mongoose";

// Behaves different than other services

export const createOption = async (option: IOption) => {
    
    let optionInstance = await Option.findOne()
    if(!optionInstance){
        optionInstance = await Option.create(option)
    }else{
        optionInstance = await updateOptions(optionInstance._id.toString(), option)
    }
    return optionInstance
}
export const getOption = async (_id: string) => {
    if (!Types.ObjectId.isValid(_id)) {
        throw new Error("")
    }
    const option = await Option.findOne({ _id });
    return option
}
export const getOptions = async () => {
    const options = await Option.findOne();
    return options
}

export const updateOptions = async (_id: String, newOption: IOption) => {
    const option = await Option.findOneAndUpdate({ _id }, newOption, { new: true });
    return option
}

export const deleteOption = async (_id: String) => {
    await Option.deleteOne({ _id });
    return {}
}