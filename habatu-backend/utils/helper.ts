import {addMinutes, getMinutes, setHours, setMinutes} from "date-fns"

export const setTimeOnDate =   (date: Date, time: string): Date =>{
    const [hours, minutes]=  time.split(":").map(n=>parseInt(n))
    let newDate = setHours(date, hours)
    newDate = setMinutes(newDate, minutes)
    return newDate
}