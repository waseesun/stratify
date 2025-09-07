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
  return apiClient.post("/users/company/", data);
};

export const createProviderUser = async (data) => {
  return apiClient.post("/users/provider/", data);
};

export const createAdminUser = async (data) => {
  return apiClient.post("/users/admin", data);
};

export const updateUser = async (id, data, isImage = false) => {
  if (isImage) {
    data.append('_method', 'PATCH');
    return apiClient.post(`/users/${id}/`, data, {}, true);
  }
  return apiClient.patch(`/users/${id}/`, data);
};

export const updateUserPortfolioLinks = async (id, data) => {
  return apiClient.patch(`/users/${id}/portfolio-links/`, data);
};

export const updateUserCategories = async (id, data) => {
  return apiClient.patch(`/users/${id}/categories/`, data);
};

export const deleteUser = async (id) => {
  return apiClient.delete(`/users/${id}/`);
};

export const getCategories = async () => {
  return apiClient.get(`/categories/`);
};

export const getCategory = async (id) => {
  return apiClient.get(`/categories/${id}/`);
};

export const createCategory = async (data) => {
  return apiClient.post("/categories/", data);
};

export const deleteCategory = async (id) => {
  return apiClient.delete(`/categories/${id}/`);
};

export const getProblems = async (queryParams = {}) => {
  const params = new URLSearchParams(queryParams);
  return apiClient.get(`/problems/?${params.toString()}`);
};

export const getCompanyProblems = async (queryParams = {}) => {
  const params = new URLSearchParams(queryParams);
  return apiClient.get(`/problems/company/?${params.toString()}`);
};

export const getProblem = async (id) => {
  return apiClient.get(`/problems/${id}/`);
};

export const createProblem = async (data) => {
  return apiClient.post("/problems/", data);
};

export const updateProblem = async (id, data) => {
  return apiClient.patch(`/problems/${id}/`, data);
};

export const deleteProblem = async (id) => {
  return apiClient.delete(`/problems/${id}/`);
};

export const getProposals = async (queryParams = {}) => {
  const params = new URLSearchParams(queryParams);
  return apiClient.get(`/proposals/?${params.toString()}`);
};

export const getProposal = async (id) => {
  return apiClient.get(`/proposals/${id}/`);
};

export const createProposal = async (data) => {
  return apiClient.post("/proposals/", data);
};

export const updateProposal = async (id, data) => {
  return apiClient.patch(`/proposals/${id}/`, data);
};

export const deleteProposal = async (id) => {
  return apiClient.delete(`/proposals/${id}/`);
};

export const getProjects = async (queryParams = {}) => {
  const params = new URLSearchParams(queryParams);
  return apiClient.get(`/projects/?${params.toString()}`);
};

export const getProject = async (id) => {
  return apiClient.get(`/projects/${id}/`);
};

export const createProject = async (data) => {
  return apiClient.post("/projects/", data);
};

export const updateProject = async (id, data) => {
  return apiClient.patch(`/projects/${id}/`, data);
};

export const deleteProject = async (id) => {
  return apiClient.delete(`/projects/${id}/`);
};

export const getTransactions = async (queryParams = {}) => {
  const params = new URLSearchParams(queryParams);
  return apiClient.get(`/transactions/?${params.toString()}`);
};

export const getTransaction = async (id) => {
  return apiClient.get(`/transactions/${id}/`);
};

export const createTransaction = async (data) => {
  return apiClient.post("/transactions/", data);
};

export const deleteTransaction = async (id) => {
  return apiClient.delete(`/transactions/${id}/`);
};

export const getReviews = async (user_id) => {
  return apiClient.get(`/reviews/${user_id}/`);
};

export const createReview = async (data) => {
  return apiClient.post("/reviews/", data);
};

export const updateReview = async (id, data) => {
  return apiClient.patch(`/reviews/${id}/`, data);
};

export const deleteReview = async (id) => {
  return apiClient.delete(`/reviews/${id}/`);
};

export const getNotifications = async (queryParams = {}) => {
  const params = new URLSearchParams(queryParams);
  return apiClient.get(`/notifications/?${params.toString()}`);
};

export const getAvailableNotification = async() => {
  return apiClient.get(`/notifications/available/`);
}

export const getNotification = async (id) => {
  return apiClient.get(`/notifications/${id}/`);
};

export const updateNotification = async (id) => {
  return apiClient.put(`/notifications/${id}/`, {});
};

export const deleteNotification = async (id) => {
  return apiClient.delete(`/notifications/${id}/`);
};

