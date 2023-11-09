import { User } from "../models";
import { IUser } from "../interfaces/user.interface";
import { Types } from "mongoose";
import { Role } from "../interfaces/role";

export const createUser = async (user: IUser) => {
  if(!user.role){
    user.role = Role.Default
  }
  return await User.create(user);
};
export const getUser = async (_id: string) => {
  if (!Types.ObjectId.isValid(_id)) {
    throw new Error("");
  }
  const user = await User.findOne({ _id });
  return user;
};
export const getUsers = async () => {
  const users = await User.find({}, { password: 0 });
  console.log(users)
  return users;
};
export const getUserByNickname = async (nickname: string) => {
  const users = await User.findOne({ nickname });
  return users;
};

export const updateUsers = async (_id: String, newUser: IUser) => {
  const user = await User.findOneAndUpdate({ _id }, newUser, { new: true });
  return user;
};

export const deleteUser = async (_id: String) => {
  await User.deleteOne({ _id });
  return {};
};
