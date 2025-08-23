import { ApiClient } from "./apiClient";

const API_URL = process.env.API_BASE_URL;
const apiClient = new ApiClient(API_URL);

// API functions
export const login = async (data) => {
  return apiClient.post("/login/", data);
};

export const logout = async () => {
  return await apiClient.post("/logout/");
};

export const getUsers = async () => {
  return apiClient.get(`/users/`);
};

export const getUser = async (id) => {
  return apiClient.get(`/users/${id}/`);
};

export const createUser = async (data) => {
  return apiClient.post("/users/", data);
};

export const createAdminUser = async (data) => {
  return apiClient.post("/admin/users/", data);
};

export const updateUser = async (id, data) => {
  return apiClient.put(`/users/${id}/`, data);
};

export const deleteUser = async (id) => {
  return apiClient.delete(`/users/${id}/`);
};