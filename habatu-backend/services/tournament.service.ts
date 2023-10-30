import { Category, Game, Hall, Option, Team, Timeslot, User } from "../models";
import {
  allocateSlots,
  gamesGenerator,
  generateTimeslots,
} from "../utils/roundRobin";
import { IHall } from "../interfaces/hall.interface";
import { ITeam } from "../interfaces/team.interface";
import { ICategory } from "../interfaces/category.interface";
import { IOption } from "../interfaces/option.interface";
import { ITimeslot } from "../interfaces/timeslot.interface";
import { format, isWithinInterval, addMinutes, getMinutes } from "date-fns";
import { ISection } from "../interfaces/section.interface";
import { IGame } from "../interfaces/game.interface";
import { IRankedTeam } from "../interfaces/rankedTeam.interface";

export const getGamesPreview = async () => {
  const teams: ITeam[] = (await Team.find({}, {}).lean()) as ITeam[];
  const categories: ICategory[] = await Category.find({}).lean();
  const halls: IHall[] = await Hall.find({}).lean({ autopopulate: true });
  const option: IOption = await Option.findOne().orFail().lean();
  const tempGames = gamesGenerator(teams, categories, halls, option);

  await Game.deleteMany({});
  await Timeslot.deleteMany({});
  const timeslots = (await Timeslot.insertMany(
    generateTimeslots(tempGames, halls, option)
  )) as ITimeslot[];
  const newGames = allocateSlots(tempGames, halls, timeslots);
  await Game.insertMany(newGames);

  return await getTournamentTable();
};

export const getTournamentTable = async () => {
  const halls: IHall[] = await Hall.find({}).lean({ autopopulate: true });
  const option: IOption = await Option.findOne().orFail().lean();
  const ts = await Timeslot.find({});
  const games = await Game.find({})
    .populate<{ hall: IHall }>("hall")
    .populate<{ timeslot: ITimeslot }>("timeslot");
  const output: any = {};
  for (let timeslot of ts) {
    output[
      `${format(timeslot.startTime, "HH:mm")} - ${format(
        timeslot.endTime,
        "HH:mm"
      )}`
    ] = {};
    output[
      `${format(timeslot.startTime, "HH:mm")} - ${format(
        timeslot.endTime,
        "HH:mm"
      )}`
    ]["id"] = timeslot._id;
    output[
      `${format(timeslot.startTime, "HH:mm")} - ${format(
        timeslot.endTime,
        "HH:mm"
      )}`
    ]["isNow"] = isWithinInterval(new Date(), {
      start: timeslot.startTime,
      end: addMinutes(
        timeslot.endTime,
        parseInt(option.breakDuration.split(":")[1])
      ),
    });
    output[
      `${format(timeslot.startTime, "HH:mm")} - ${format(
        timeslot.endTime,
        "HH:mm"
      )}`
    ]["items"] = {};
    for (let hall of halls) {
      output[
        `${format(timeslot.startTime, "HH:mm")} - ${format(
          timeslot.endTime,
          "HH:mm"
        )}`
      ]["items"][`${hall.name}`] = {
        id: hall._id,
        items: games.filter(
          (g) =>
            g.timeslot!._id!.toString() == timeslot._id!.toString() &&
            g.hall!._id!.toString() == hall._id!.toString()
        ),
      };
    }
  }
  return output;
};
export const getTableByGroupId = async (_id: string) => {
  const games = await Game.find({ $or: [{ teamA: _id }, { teamB: _id }] })
    .populate<{ hall: IHall }>("hall")
    .populate<{ timeslot: ITimeslot }>("timeslot")
    .populate<{ teams: ITeam }>("teamA")
    .populate<{ teams: ITeam }>("teamB");
  const output: Record<string, any> = {};
  for (let game of games) {
    output[
      `${format(game.timeslot.startTime, "HH:mm")} - ${format(
        game.timeslot.endTime,
        "HH:mm"
      )}`
    ] = game;
  }
  return output;
};

export const getTimePreview = async () => {
  await getGamesPreview();
  const teams: ITeam[] = (await Team.find({}, {}).lean()) as ITeam[];
  const categories: ICategory[] = await Category.find({}).lean();
  const halls: IHall[] = await Hall.find({}).lean({ autopopulate: true });
  const option: IOption = await Option.findOne().orFail().lean();
  const games = await Game.find({})
    .populate<{ hall: IHall }>("hall")
    .populate<{ timeslot: ITimeslot }>("timeslot");
    

  return {
    amountOfGames: games.length,
    lastGame: games[games.length - 1].timeslot.endTime,
  };
};

export const getTournamentRanking = async (): Promise<
  Record<string, IRankedTeam[]>
> => {
  const teams = await Team.find({}, {})
    .lean()
    .populate<{ section: ISection }>("section");
  const categories: ICategory[] = await Category.find({}).lean();
  const games = await Game.find({}).lean({ autopopulate: false });
  const rankedTeams: IRankedTeam[] = [...teams];
  for (let team of rankedTeams) {
    var tournamentPoints = 0;
    var pointsPro = 0;
    var pointsCon = 0;
    for (let game of games) {
      if (game.teamA.toString() == team._id.toString()) {
        pointsPro += game.pointsTeamA;
        pointsCon += game.pointsTeamB;
        if (game.pointsTeamA > game.pointsTeamB) {
          tournamentPoints += 2;
        }
        if (game.pointsTeamA == game.pointsTeamB) {
          tournamentPoints += 1;
        }
      }
      if (game.teamB.toString() == team._id.toString()) {
        pointsPro += game.pointsTeamB;
        pointsCon += game.pointsTeamA;
        if (game.pointsTeamA < game.pointsTeamB) {
          tournamentPoints += 2;
        }
        if (game.pointsTeamA == game.pointsTeamB) {
          tournamentPoints += 1;
        }
      }
    }
    team.tournamentPoints = tournamentPoints;
    team.pointsPro = pointsPro;
    team.pointsCon = pointsCon;
  }
  const comparefunction = (a: any, b: any): number => {
    if (a.tournamentPoints > b.tournamentPoints) {
      return -1;
    } else if (b.tournamentPoints > a.tournamentPoints) {
      return 1;
    } else if (a.pointsPro > b.pointsPro) {
      return -1;
    } else if (b.pointsPro > a.pointsPro) {
      return 1;
    } else if (a.pointsCon < b.pointsCon) {
      return -1;
    } else if (b.pointsCon < a.pointsCon) {
      return 1;
    } else {
      return 0;
    }
  };
  const rankedTeamsByCategory: Record<string, IRankedTeam[]> = {};
  for (let category of categories) {
    rankedTeamsByCategory[category.name] = rankedTeams
      .filter((t: any) => t.category == category._id.toString())
      .sort(comparefunction);
  }

  return rankedTeamsByCategory;
};
export const createFinals = async () => {
  const halls: IHall[] = await Hall.find({}).lean({ autopopulate: true });
  const timeslots: ITimeslot[] = await Timeslot.find({}).sort({ startTime: 1 });
  const games = await Game.find({})
    .populate<{ timeslot: ITimeslot }>("timeslot")
    .sort({ "timeslot.startTime": -1 });
  const lastGameSlotId = games[games.length - 1].timeslot._id!;
  console.log(timeslots.map((t) => t.startTime));
  var slotIndex: number =
    timeslots.map((t) => t._id?.toString()).indexOf(lastGameSlotId.toString()) +
    1;
  const rankedTeamsByCategory = await getTournamentRanking();
  const finals: IGame[] = [];
  for (let category in rankedTeamsByCategory) {
    const teams: IRankedTeam[] = rankedTeamsByCategory[category];
    finals.push({
      teamA: teams[0]._id.toString(),
      teamB: teams[1]._id.toString(),
      category: teams[0].category!.toString(),
      type: "grandFinale",
      pointsTeamA: 0,
      pointsTeamB: 0,
    });
    finals.push({
      teamA: teams[2]._id.toString(),
      teamB: teams[3]._id.toString(),
      category: teams[2].category!.toString(),
      type: "petiteFinale",
      pointsTeamA: 0,
      pointsTeamB: 0,
    });
  }
  for (let [i, game] of finals.entries()) {
    if (slotIndex < timeslots.length - 1 && i % halls.length == 0) {
      slotIndex++;
    }

    game.timeslot = timeslots[slotIndex]._id!.toString();
    console.log(slotIndex);

    game.hall = halls[i % halls.length]._id.toString();
  }

  await Game.insertMany(finals);
};

export const specifyReferee = async (gameId: string, userId: string) => {
  const user = await User.findByIdAndUpdate(
    userId,
    {
      $addToSet: {
        refereeGames: gameId,
      },
    }
  );
  console.log(user);
};
