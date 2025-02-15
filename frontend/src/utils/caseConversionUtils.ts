import { mapKeys, isPlainObject, snakeCase, camelCase } from "lodash";

export const snakeToCamelObject = (obj: any): any => {
  if (isPlainObject(obj)) {
    const newObject = mapKeys(obj, (_: any, key: string) => camelCase(key));
    Object.keys(newObject).forEach((key: string) => {
      newObject[key] = snakeToCamelObject(newObject[key]);
    });
    return newObject;
  } else if (Array.isArray(obj)) {
    return obj.map((element) => snakeToCamelObject(element));
  }
  return obj;
};

export const camelToSnakeObject = (obj: any): any => {
  if (isPlainObject(obj)) {
    const newObject = mapKeys(obj, (_: any, key: string) => snakeCase(key));
    Object.keys(newObject).forEach((key: string) => {
      newObject[key] = camelToSnakeObject(newObject[key]);
    });
    return newObject;
  } else if (Array.isArray(obj)) {
    return obj.map((element) => camelToSnakeObject(element));
  }
  return obj;
};
