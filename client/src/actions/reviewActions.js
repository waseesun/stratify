"use server";
import {
  getReviews,
  createReview,
  updateReview,
  deleteReview
} from "@/libs/api";

export const actionError = async (response) => {
  if (typeof response.error === "object") {
    const errorMessages = {};

    if (response.error.reviewee_id) {
      errorMessages["reviewee_id"] = response.error.reviewee_id;
    }

    if (response.error.rating) {
      errorMessages["rating"] = response.error.rating;
    }

    if (response.error.comment) {
      errorMessages["comment"] = response.error.comment;
    }

    return { error: errorMessages };
  }

  return { error: { error: response.error } };
};

export const getReviewsAction = async (user_id) => {
  try {
    const response = await getReviews(user_id);

    if (response.error) {
      return { error: response.error };
    }

    return { data: response };
  } catch (error) {
    return { error: error.error || "An unexpected Error occured." };
  }
};

export const createReviewAction = async (formData) => {
  const reviewee_id = formData.get("reviewee_id");
  const rating = formData.get("rating");
  const comment = formData.get("comment");

  const data = {
    reviewee_id,
    rating,
    comment,
  };

  try {
    const response = await createReview(data);

    if (response.error) {
      return actionError(response);
    }

    return { success: response.success };
  } catch (error) {
    console.error(error);
    return { error: error.error || "An unexpected Error occured." };
  }
};

export const updateReviewAction = async (id, formData) => {
  const rating = formData.get("rating");
  const comment = formData.get("comment");

  const data = {
    rating,
    comment,
  };

  try {
    const response = await updateReview(id, data);
    console.log(response);

    if (response.error) {
      return actionError(response);
    }

    return { success: response.success };
  } catch (error) {
    console.error(error);
    return { error: error.error || "An unexpected Error occured." };
  }
};


export const deleteReviewAction = async (id) => {
  try {
    const response = await deleteReview(id);

    if (response.error) {
      return { error: response.error };
    }
    
    return { success: "Review deleted successfully" };
  } catch (error) {
    console.error(error);
    return { error: error.error || "An unexpected Error occured." };
  }
};
