import { Category, Game, Hall, Option, Team, Timeslot, User } from "../models";
import {
  allocateSlots,
  gamesGenerator,
  generateTimeslots,
} from "../utils/roundRobin";
import { IHall } from "../interfaces/hall.interface";
import { ITeam } from "../interfaces/team.interface";
import { Role } from "../interfaces/role";
import { IUser } from "../interfaces/user.interface";
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
    lastGame: games[games.length - 1].timeslot?.endTime,
  };
};


export const assignGamesToReferees= async (): Promise<Record<string, IGame[]>>  => {
    const users = await User.find({}).lean({ autopopulate: false });
    const teams = await Team.find({}).lean({ autopopulate: false });
    const games = await Game.find({}).lean({ autopopulate: false });
    const referees = users.filter(user => user.role === Role.Referee);
    let assignments: Record<string, IGame[]> = {};
  

    // Initialize assignment record for each referee
    referees.forEach(referee => {
        assignments[referee.nickname] = [];
    });

    for (const game of games) {
        for (const referee of referees) {
            if (isEligibleReferee(referee, game, teams, games)) {
                assignments[referee.nickname].push(game);
                referees.push(...referees.splice(referees.indexOf(referee), 1));
                
                break;
            }
        }
    }
    for (const [nickname, games] of Object.entries(assignments)) {
      await User.findOneAndUpdate({ nickname }, { $set: { refereeGames: games.map(g=>g._id) } });
  }

    return assignments;
}

const isEligibleReferee = (referee: IUser, game: IGame, teams: ITeam[], games: IGame[]): boolean => {
    const refereeTeam = teams.find(team => team._id?.toString() == referee.team?.toString());

    if (!refereeTeam) return false;
  
    // Check if the referee's team is playing in the game
    if (game.teamA === referee.team || game.teamB === referee.team) return false;

    // Check for same section constraint
    const teamA = teams.find(team => team._id.toString() === game.teamA);
    const teamB = teams.find(team => team._id.toString() === game.teamB);

    if (teamA?.section?.toString() === refereeTeam.section?.toString() || teamB?.section?.toString() === refereeTeam.section?.toString()) return false;

    // Check timeslot constraint
    const refereeTeamGame = games.find(g => (g.teamA === referee.team || g.teamB === referee.team) && g.timeslot === game.timeslot);
    if (refereeTeamGame) return false;

    return true;
  
}

// Usage: Call `assignGamesToReferees` with users, games, and teams data


export const getTournamentRanking = async (): Promise<
  Record<string, IRankedTeam[]>
> => {
  const teams = await Team.find({}, {})
    .lean()
    .populate<{ section: ISection }>("section");
  const categories: ICategory[] = await Category.find({}).lean();
  const timeslots: ITimeslot[] = await Timeslot.find({})
  const games = await Game.find({}).lean({ autopopulate: false });
  const rankedTeams: IRankedTeam[] = [...teams];
  // Get only timeslots which have already been played
  const currentTime = new Date();
  const currentTimeString = currentTime.toTimeString();
  const filteredTimeslotsIds = timeslots.filter(timeslot => {
    return timeslot.endTime.toTimeString() < currentTimeString;
  }).map(timeslot=>timeslot._id?.toString());
  for (let team of rankedTeams) {
    var tournamentPoints = 0;
    var pointsPro = 0;
    var pointsCon = 0;
    var gamesPlayed = 0;
    for (let game of games) {
      if(!filteredTimeslotsIds.includes(game.timeslot?.toString())){
        continue;
      }
      if (game.teamA.toString() == team._id.toString()) {
        pointsPro += game.pointsTeamA;
        pointsCon += game.pointsTeamB;
        if (game.pointsTeamA > game.pointsTeamB) {
          tournamentPoints += 3;
        }
        if (game.pointsTeamA == game.pointsTeamB) {
          tournamentPoints += 1;
        }
        gamesPlayed++;
      }
      if (game.teamB.toString() == team._id.toString()) {
        pointsPro += game.pointsTeamB;
        pointsCon += game.pointsTeamA;
        if (game.pointsTeamA < game.pointsTeamB) {
          tournamentPoints += 3;
        }
        if (game.pointsTeamA == game.pointsTeamB) {
          tournamentPoints += 1;
        }
        gamesPlayed++;
      }
      
      
    }
    team.tournamentPoints = tournamentPoints;
    team.pointsPro = pointsPro;
    team.pointsCon = pointsCon;
    team.pointsDifference = pointsPro - pointsCon;
    team.gamesPlayed = gamesPlayed;
  }
  const comparefunction = (a: any, b: any): number => {
    if (a.tournamentPoints > b.tournamentPoints) {
      return -1;
    } else if (b.tournamentPoints > a.tournamentPoints) {
      return 1;
    } else if (a.pointsDifference > b.pointsDifference) {
      return -1;
    } else if (b.pointsDifference > a.pointsDifference) {
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

    game.hall = halls[i % halls.length]._id.toString();
  }

  await Game.insertMany(finals);
};

export const specifyReferee = async (gameId: string, userId: string) => {
  const game = await Game.findById(gameId);
  if (!game) {
    throw new Error("Game not found");
  }

  await Game.findByIdAndUpdate(
    gameId,
    {
      $addToSet: {
        referees: userId,
      },
    }
  )

  const user = await User.findByIdAndUpdate(
    userId,
    {
      $addToSet: {
        refereeGames: gameId,
      },
    }
  );
};
