import { ref, Ref } from 'vue';
import axios, { AxiosResponse } from 'axios';
import applyCaseMiddleware from 'axios-case-converter';

export function useApi<T>(endpoint: string) {
  const data: Ref<T | null> = ref(null);
  const dataList: Ref<T[]> = ref([]);
  const loading = ref(false);
  const error = ref<string | null>(null);
  const backendUrl = 'http://localhost:8000/api/';
  const client = applyCaseMiddleware(axios.create());
  client.interceptors.request.use((config) => {
    const token = localStorage.getItem('token');
    if (token) {
      config.headers.Authorization = `Bearer ${token}`;
    }
    return config;
  }, (error) => {
    return Promise.reject(error);
  });

  /**
   * Fetch data from the API.
   * @param id Optional ID to fetch a specific item.
   */
  const fetchData = async (id?: number | string, singleItem: boolean = false) => {
    loading.value = true;
    error.value = null;

    try {
      const url = id ? `${endpoint}/${id}` : endpoint;
      const response: AxiosResponse<T | T[]> = await client.get(`${backendUrl}${url}`);

      if (id || singleItem) {
        data.value = response.data as T;
      } else {
        dataList.value = response.data as T[];
      }
    } catch (e) {
      handleError(e);
    } finally {
      loading.value = false;
    }
  };

  /**
   * Create new data.
   * @param payload The data to create.
   */
  const createData = async (payload: T) => {
    loading.value = true;
    error.value = null;

    try {
      const response: AxiosResponse<T> = await client.post(`${backendUrl}${endpoint}`, payload);
      data.value = response.data;
      return response.data;
    } catch (e) {
      handleError(e);
    } finally {
      loading.value = false;
    }
  };

  /**
   * Update existing data.
   * @param id The ID of the item to update.
   * @param payload The updated data.
   */
  const updateData = async (id: number | string, payload: Partial<T>) => {
    loading.value = true;
    error.value = null;

    try {
      const response: AxiosResponse<T> = await client.put(`${backendUrl}${endpoint}/${id}`, payload);
      data.value = response.data;
    } catch (e) {
      handleError(e);
    } finally {
      loading.value = false;
    }
  };

  /**
   * Delete data.
   * @param id The ID of the item to delete.
   */
  const deleteData = async (id: number | string) => {
    loading.value = true;
    error.value = null;

    try {
      await client.delete(`${backendUrl}${endpoint}/${id}`);
    } catch (e) {
      handleError(e);
    } finally {
      loading.value = false;
    }
  };

  /**
   * Handle errors from Axios requests.
   * @param error The error object.
   */
  const handleError = (e: unknown) => {
    throw e;
    /* if (axios.isAxiosError(e) && e.response) {
      error.value = e.response.statusText || 'An error occurred.';
    } else {
      error.value = 'An unknown error occurred.';
    } */
  };

   /**
   * General POST method.
   * @param customEndpoint The endpoint to post to.
   * @param payload The data to post.
   */
   const postData = async (payload: any) => {
    loading.value = true;
    error.value = null;

    try {
      const response: AxiosResponse<any> = await client.post(
        `${backendUrl}${endpoint}`,
        payload
      );
      return response.data;
    } catch (e) {
      handleError(e);
    } finally {
      loading.value = false;
    }
  }

  return {
    data,
    dataList,
    loading,
    error,
    fetchData,
    createData,
    updateData,
    deleteData,
    postData,
  };
}