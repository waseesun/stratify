"use server";
import {
  getProjects,
  getProject,
  createProject,
  updateProject,
  deleteProject,
} from "@/libs/api";

export const actionError = async (response) => {
  if (typeof response.error === "object") {
    const errorMessages = {};

    if (response.error.problem_id) {
      errorMessages["problem"] = response.error.problem_id;
    }

    if (response.error.propasal_id) {
      errorMessages["propasal"] = response.error.propasal_id;
    }

    if (response.error.fee) {
      errorMessages["fee"] = response.error.fee;
    }

    if (response.error.start_date) {
      errorMessages["start_date"] = response.error.start_date;
    }

    if (response.error.end_date) {
      errorMessages["end_date"] = response.error.end_date;
    }

    if (response.error.status) {
      errorMessages["status"] = response.error.status;
    }

    // Combine messages into a single string with \n between each
    return { error: errorMessages };
  }

  // If it's not an object, return the error as is (string or other propasal_id)
  return { error: { error: response.error } };
};

export const getProjectsAction = async (queryParams = {}) => {
  try {
    const response = await getProjects(queryParams);

    if (response.error) {
      return { error: response.error };
    }

    return {
      data: response.data,
      pagination: {
        count: response.total,
        total_pages: Math.ceil(response.total / response.per_page),
        next: response.next_page_url ? new URL(response.next_page_url).search : null,
        previous: response.prev_page_url ? new URL(response.prev_page_url).search : null,
      },
    };
  } catch (error) {
    console.error(error);
    return { error: error.message || "Failed to fetch users." };
  }
};

export const getProjectAction = async (id) => {
  try {
    const response = await getProject(id);

    if (response.error) {
      return { error: response.error };
    }

    return { data: response };
  } catch (error) {
    console.error(error);
    return { error: error.message || "Failed to fetch user." };
  }
};

export const createProjectAction = async (formData) => {
  const problem_id = formData.get("problem");
  const propasal_id = formData.get("propasal");
  const fee = formData.get("fee");
  const start_date = formData.get("start_date");
  const end_date = formData.get("end_date");

  const data = {
    problem_id,
    propasal_id,
    fee,
    start_date,
    end_date,
  };

  try {
    const response = await createProject(data);

    if (response.error) {
      return actionError(response);
    }

    return { success: response.success };
  } catch (error) {
    console.error(error);
    return { error: error.message || "Failed to create user." };
  }
};

export const updateProjectAction = async (id, formData) => {
  const fee = formData.get("fee");
  const start_date = formData.get("start_date");
  const end_date = formData.get("end_date");
  const status = formData.get("status");

  const data = {
    ...(start_date && { start_date }),
    ...(end_date && { end_date }),
    ...(fee && { fee }),
    ...(status && { status }),
  };

  try {
    const response = await updateProject(id, data);

    if (response.error) {
      return actionError(response);
    }

    return { success: response.success };
  } catch (error) {
    console.error(error);
    return { error: error.message || "Failed to update user." };
  }
};

export const deleteProjectAction = async (id) => {
  try {
    const response = await deleteProject(id);

    if (response.error) {
      return { error: response.error };
    }
    
    return { success: "Project deleted successfully" };
  } catch (error) {
    console.error(error);
    return { error: error.message || "Failed to delete user." };
  }
};
