import { Section } from "../models";
import { ISection } from '../interfaces/section.interface';
import { Types } from "mongoose";

export const createSection = async (section: ISection) => {
    return await Section.create(section)
}
export const getSection = async (_id: string) => {
    if (!Types.ObjectId.isValid(_id)) {
        throw new Error("")
    }
    const section = await Section.findOne({ _id });
    return section
}
export const getSections = async () => {
    const sections = await Section.find({});
    return sections
}

export const updateSections = async (_id: String, newSection: ISection) => {
    const section = await Section.findOneAndUpdate({ _id }, newSection, { new: true });
    return section
}

export const deleteSection = async (_id: String) => {
    await Section.deleteOne({ _id });
    return {}
}