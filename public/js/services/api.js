import axios from 'axios';
import authService from './auth';

class ApiService {
    constructor() {
        this.baseURL = '/api';
    }

    async get(url, params = {}) {
        try {
            const response = await axios.get(`${this.baseURL}${url}`, { params });
            return response.data;
        } catch (error) {
            this.handleError(error);
            throw error;
        }
    }

    async post(url, data = {}) {
        try {
            const response = await axios.post(`${this.baseURL}${url}`, data);
            return response.data;
        } catch (error) {
            this.handleError(error);
            throw error;
        }
    }

    async put(url, data = {}) {
        try {
            const response = await axios.put(`${this.baseURL}${url}`, data);
            return response.data;
        } catch (error) {
            this.handleError(error);
            throw error;
        }
    }

    async delete(url) {
        try {
            const response = await axios.delete(`${this.baseURL}${url}`);
            return response.data;
        } catch (error) {
            this.handleError(error);
            throw error;
        }
    }

    handleError(error) {
        if (error.response?.status === 401) {
            authService.logout();
        }
        throw error;
    }
}

export default new ApiService(); 