"use server";
import {
  getProblems,
  getAllProblems,
  getCompanyProblems,
  getProblem,
  createProblem,
  updateProblem,
  deleteProblem,
} from "@/libs/api";

export const actionError = async (response) => {
  if (typeof response.error === "object") {
    const errorMessages = {};

    for (const key in response.error) {
      if (key.startsWith("skills")) {
        if (!errorMessages.skills) {
          errorMessages.skills = [];
        }
        errorMessages.skills.push(...response.error[key]);
      }
    }

    if (response.error.company_id) {
      errorMessages["company"] = response.error.company_id;
    }

    if (response.error.category_id) {
      errorMessages["category"] = response.error.category_id;
    }

    if (response.error.description) {
      errorMessages["description"] = response.error.description;
    }

    if (response.error.title) {
      errorMessages["title"] = response.error.title;
    }

    if (response.error.budget) {
      errorMessages["budget"] = response.error.budget;
    }

    if (response.error.status) {
      errorMessages["status"] = response.error.status;
    }

    if (response.error.timeline_value) {
      errorMessages["timeline_value"] = response.error.timeline_value;
    }

    if (response.error.timeline_unit) {
      errorMessages["timeline_unit"] = response.error.timeline_unit;
    }

    // Combine messages into a single string with \n between each
    return { error: errorMessages };
  }

  // If it's not an object, return the error as is (string or other category_id)
  return { error: { error: response.error } };
};

export const getProblemsAction = async (queryParams = {}) => {
  try {
    const response = await getProblems(queryParams);

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

export const getCompanyProblemsAction = async (queryParams = {}) => {
  try {
    const response = await getCompanyProblems(queryParams);

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
}

export const getAllProblemsAction = async () => {
  try {
    const response = await getAllProblems();

    if (response.error) {
      return { error: response.error };
    }

    return { data: response };
  } catch (error) {
    console.error(error);
    return { error: error.message || "Failed to fetch users." };
  }
}

export const getProblemAction = async (id) => {
  try {
    const response = await getProblem(id);

    if (response.error) {
      return { error: response.error };
    }

    return { data: response };
  } catch (error) {
    console.error(error);
    return { error: error.message || "Failed to fetch user." };
  }
};

export const createProblemAction = async (formData) => {
  const company_id = formData.get("company");
  const category_id = formData.get("category");
  const description = formData.get("description");
  const title = formData.get("title");
  const budget = formData.get("budget");
  const timeline_value = formData.get("timeline_value");
  const timeline_unit = formData.get("timeline_unit");
  const skills = formData.getAll("skills[]");

  const data = {
    company_id,
    category_id,
    description,
    title,
    skills,
    budget,
    timeline_unit,
    timeline_value,
  };

  try {
    const response = await createProblem(data);

    if (response.error) {
      return actionError(response);
    }

    return { success: response.success };
  } catch (error) {
    console.error(error);
    return { error: error.message || "Failed to create user." };
  }
};

export const updateProblemAction = async (id, formData) => {
  const category_id = formData.get("category");
  const description = formData.get("description");
  const title = formData.get("title");
  const budget = formData.get("budget");
  const timeline_value = formData.get("timeline_value");
  const timeline_unit = formData.get("timeline_unit");
  const skills = formData.getAll("skills[]");
  const status = formData.get("status");

  const data = {
    ...(category_id && { category_id }),
    ...(title && { title }),
    ...(budget && { budget }),
    ...(description && { description }),
    ...(timeline_unit && { timeline_unit }),
    ...(timeline_value && { timeline_value }),
    ...(skills.length > 0 && { skills }),
    ...(status && { status }),
  };

  try {
    const response = await updateProblem(id, data);

    if (response.error) {
      return actionError(response);
    }

    return { success: response.success };
  } catch (error) {
    console.error(error);
    return { error: error.message || "Failed to update user." };
  }
};

export const deleteProblemAction = async (id) => {
  try {
    const response = await deleteProblem(id);

    if (response.error) {
      return { error: response.error };
    }
    
    return { success: "Problem deleted successfully" };
  } catch (error) {
    console.error(error);
    return { error: error.message || "Failed to delete user." };
  }
};
