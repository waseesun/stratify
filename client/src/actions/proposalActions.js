"use server";
import {
  getProposals,
  getProposal,
  createProposal,
  updateProposal,
  deleteProposal,
} from "@/libs/api";

export const actionError = async (response) => {
  if (typeof response.error === "object") {
    const errorMessages = {};

    for (const key in response.error) {
      if (key.startsWith("docs")) {
        if (!errorMessages.docs) {
          errorMessages.docs = [];
        }
        errorMessages.docs.push(...response.error[key]);
      }
    }

    if (response.error.provider_id) {
      errorMessages["provider"] = response.error.provider_id;
    }

    if (response.error.problem_id) {
      errorMessages["problem"] = response.error.problem_id;
    }

    if (response.error.description) {
      errorMessages["description"] = response.error.description;
    }

    if (response.error.title) {
      errorMessages["title"] = response.error.title;
    }

    // Combine messages into a single string with \n between each
    return { error: errorMessages };
  }

  // If it's not an object, return the error as is (string or other problem_id)
  return { error: { error: response.error } };
};

export const getProposalsAction = async (queryParams = {}) => {
  try {
    const response = await getProposals(queryParams);

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

export const getProposalAction = async (id) => {
  try {
    const response = await getProposal(id);

    if (response.error) {
      return { error: response.error };
    }

    return { data: response };
  } catch (error) {
    console.error(error);
    return { error: error.message || "Failed to fetch user." };
  }
};

export const createProposalAction = async (formData) => {
  try {
    const response = await createProposal(formData);

    if (response.error) {
      return actionError(response);
    }

    return { success: response.success };
  } catch (error) {
    console.error(error);
    return { error: error.message || "Failed to create user." };
  }
};

export const updateProposalAction = async (id, formData) => {
  try {
    const response = await updateProposal(id, formData);

    if (response.error) {
      return actionError(response);
    }

    return { success: response.success };
  } catch (error) {
    console.error(error);
    return { error: error.message || "Failed to update user." };
  }
};

export const deleteProposalAction = async (id) => {
  try {
    const response = await deleteProposal(id);

    if (response.error) {
      return { error: response.error };
    }
    
    return { success: "Proposal deleted successfully" };
  } catch (error) {
    console.error(error);
    return { error: error.message || "Failed to delete user." };
  }
};
