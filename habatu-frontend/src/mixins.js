import axios from 'axios'
const api = axios.create({
    baseURL: 'http://localhost:8000',
    headers:{
       accept: "application/json"
    },
    timeout: 1000
  });
export const mixin = {
    methods:{
        async callApi(method, url, data){
            console.log(data)
            try{
                const response = await api({method,url,data  })
                return response
            }catch(e){
                return JSON.stringify(e, Object.getOwnPropertyNames(e))
            }

        }
    }
}