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

export const getUsers = async (queryParams = {}) => {
  const params = new URLSearchParams(queryParams);
  return apiClient.get(`/users/?${params.toString()}`);
};

export const getUser = async (id) => {
  return apiClient.get(`/users/${id}/`);
};

export const createCompanyUser = async (data) => {
  return apiClient.post("/users/company/", data, {}, true);
};

export const createProviderUser = async (data) => {
  return apiClient.post("/users/provider/", data, {}, true);
};

export const createAdminUser = async (data) => {
  return apiClient.post("/users/admin", data, {}, true);
};

export const updateUser = async (id, data) => {
  return apiClient.patch(`/users/${id}/`, data, {}, true);
};

export const updateUserPortfolioLinks = async (id, data) => {
  return apiClient.patch(`/users/${id}/portfolio-links`, data);
};

export const updateUserCategories = async (id, data) => {
  return apiClient.patch(`/users/${id}/categories`, data);
};

export const deleteUser = async (id) => {
  return apiClient.delete(`/users/${id}/`);
};