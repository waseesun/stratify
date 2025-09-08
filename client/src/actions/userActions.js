"use server";
import {
  getUsers,
  getUser,
  createCompanyUser,
  createProviderUser,
  createAdminUser,
  updateUser,
  updateUserCategories,
  updateUserPortfolioLinks,
  deleteUser,
} from "@/libs/api";
import { deleteSessionCookie } from "@/libs/cookie";

export const actionError = async (response) => {
  if (typeof response.error === "object") {
    const errorMessages = {};

    for (const key in response.error) {
      if (key.startsWith("links")) {
        if (!errorMessages.links) {
          errorMessages.links = [];
        }
        errorMessages.links.push(...response.error[key]);
      }
      if (key.startsWith("categories")) {
        if (!errorMessages.categories) {
          errorMessages.categories = [];
        }
        errorMessages.categories.push(...response.error[key]);
      }
    }

    if (response.error.email) {
      errorMessages["email"] = response.error.email;
    }

    if (response.error.username) {
      errorMessages["username"] = response.error.username;
    }

    if (response.error.first_name) {
      errorMessages["first_name"] = response.error.first_name;
    }

    if (response.error.last_name) {
      errorMessages["last_name"] = response.error.last_name;
    }

    if (response.error.address) {
      errorMessages["address"] = response.error.address;
    }

    if (response.error.password) {
      errorMessages["password"] = response.error.password;
    }

    if (response.error.description) {
      errorMessages["description"] = response.error.description;
    }

    if (response.error.image_url) {
      errorMessages["image_url"] = response.error.image_url;
    }

    return { error: errorMessages };
  }

  return { error: { error: response.error } };
};

export const getUsersAction = async (queryParams = {}) => {
  try {
    const response = await getUsers(queryParams);

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

export const getUserAction = async (id) => {
  try {
    const response = await getUser(id);

    if (response.error) {
      return { error: response.error };
    }

    return { data: response };
  } catch (error) {
    console.error(error);
    return { error: error.message || "Failed to fetch user." };
  }
};

export const createUserAction = async (formData, userType) => {
  const email = formData.get("email");
  const username = formData.get("username");
  const first_name = formData.get("first_name");
  const last_name = formData.get("last_name");
  const address = formData.get("address");
  const description = formData.get("description");
  const password = formData.get("password");
  const password_confirmation = formData.get("password_confirmation");

  const errors = {};

  if (!email) {
    errors.email = "Email is required.";
  } else if (!email.includes("@")) {
    errors.email = "Invalid email format.";
  }

  if (!password) {
    errors.password = "Password is required.";
  }

  if (!password_confirmation) {
    errors.password_confirmation = "Password confirmation is required.";
  }

  if (password !== password_confirmation) {
    errors.password_confirmation = "Passwords do not match.";
  }

  if (Object.keys(errors).length > 0) {
    return { error: errors };
  }

  const data = {
    email,
    ...(username && { username }),
    ...(first_name && { first_name }),
    ...(last_name && { last_name }),
    ...(address && { address }),
    ...(description && { description }),
    password,
    password_confirmation,
  };

  try {
    let response;
    
    if (userType === "company") {
      response = await createCompanyUser(data);
    } else if (userType === "provider") {
      response = await createProviderUser(data);
    } else if (userType === "admin") {
      response = await createAdminUser(data);
    } else {
      return { error: "Invalid user type." };
    }

    if (response.error) {
      return actionError(response);
    }

    return { success: response.success };
  } catch (error) {
    console.error(error);
    return { error: error.message || "Failed to create user." };
  }
};

export const updateUserAction = async (id, formData) => {
  try {
    let data, response;
    console.log(formData);
    let image_url = formData.get("image_url");

    if (image_url.size > 0) {
      response = await updateUser(id, formData, true);
    } else {
      data = {
        ...(formData.get("email") && { email: formData.get("email") }),
        ...(formData.get("username") && { username: formData.get("username") }),
        ...(formData.get("first_name") && { first_name: formData.get("first_name") }),
        ...(formData.get("last_name") && { last_name: formData.get("last_name") }),
        ...(formData.get("address") && { address: formData.get("address") }),
        ...(formData.get("description") && { description: formData.get("description") }),
        ...(formData.get("password") && { password: formData.get("password") }),
        ...(formData.get("password_confirmation") && { password_confirmation: formData.get("password_confirmation") }),
      };

      response = await updateUser(id, data);
    }

    if (response.error) {
      return actionError(response);
    }

    return { success: response.success };
  } catch (error) {
    console.error(error);
    return { error: error.message || "Failed to update user." };
  }
};

export const updateUserPortfolioLinksAction = async (id, formData) => {
  try {
    const response = await updateUserPortfolioLinks(id, formData);
    console.log(response);
    if (response.error) {
      return actionError(response);
    }

    return { success: response.success };
  } catch (error) {
    console.error(error);
    return { error: error.message || "Failed to update user portfolio links." };
  }
}

export const updateUserCategoriesAction = async (id, formData) => {
  try {
    const response = await updateUserCategories(id, formData);

    if (response.error) {
      return actionError(response);
    }

    return { success: response.success };
  } catch (error) {
    console.error(error);
    return { error: error.message || "Failed to update user categories." };
  }
}

export const deleteUserAction = async (id) => {
  try {
    const response = await deleteUser(id);

    if (response.error) {
      return { error: response.error };
    }
    
    await deleteSessionCookie();
    
    return { success: "User deleted successfully" };
  } catch (error) {
    console.error(error);
    return { error: error.message || "Failed to delete user." };
  }
};
