"use server";
import {
  getTransactions,
  getTransaction,
  createTransaction,
  deleteTransaction
} from "@/libs/api";

export const actionError = async (response) => {
  if (typeof response.error === "object") {
    const errorMessages = {};

    if (response.error.project_id) {
      errorMessages["project"] = response.error.project_id;
    }

    if (response.error.milestone_name) {
      errorMessages["milestone_name"] = response.error.milestone_name;
    }

    if (response.error.amount) {
      errorMessages["amount"] = response.error.amount;
    }

    return { error: errorMessages };
  }

  return { error: { error: response.error } };
};

export const getTransactionsAction = async (queryParams = {}) => {
  try {
    const response = await getTransactions(queryParams);

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
    return { error: error.message || "An unexpected Error occured." };
  }
};

export const getTransactionAction = async (id) => {
  try {
    const response = await getTransaction(id);

    if (response.error) {
      return { error: response.error };
    }

    return { data: response };
  } catch (error) {
    console.error(error);
    return { error: error.message || "An unexpected Error occured." };
  }
};

export const createTransactionAction = async (formData) => {
  const project_id = formData.get("project");
  const milestone_name = formData.get("milestone_name");
  const amount = formData.get("amount");

  const data = {
    project_id,
    milestone_name,
    amount,
  };

  try {
    const response = await createTransaction(data);

    if (response.error) {
      return actionError(response);
    }

    return { success: response.success };
  } catch (error) {
    console.error(error);
    return { error: error.message || "An unexpected Error occured." };
  }
};


export const deleteTransactionAction = async (id) => {
  try {
    const response = await deleteTransaction(id);

    if (response.error) {
      return { error: response.error };
    }
    
    return { success: response.success };
  } catch (error) {
    console.error(error);
    return { error: error.message || "An unexpected Error occured." };
  }
};
