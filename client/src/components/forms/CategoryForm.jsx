"use client"

import { useState } from "react"
import { updateUserCategoriesAction } from "@/actions/userActions"
import {UpdateButton} from "@/components/buttons/Buttons"
import styles from "./CategoryForm.module.css"

export default function CategoryForm({ userId }) {
  const [errors, setErrors] = useState({})
  const [success, setSuccess] = useState("")

  const handleSubmit = async (formData) => {
    setErrors({})
    setSuccess("")

    try {
      const result = await updateUserCategoriesAction(userId, formData)

      if (result.error) {
        setErrors(result.error)
      } else if (result.success) {
        setSuccess(result.success)
      }
    } catch (error) {
      setErrors({ general: "An unexpected error occurred" })
    }
  }

  const categories = [
    "Web Development",
    "Mobile Development",
    "UI/UX Design",
    "Graphic Design",
    "Digital Marketing",
    "Content Writing",
    "Data Analysis",
    "Project Management",
    "Consulting",
    "Photography",
    "Video Editing",
    "Translation",
  ]

  return (
    <div className={styles.container}>
      <h2 className={styles.title}>Update Categories</h2>

      {success && <div className={styles.success}>{success}</div>}
      {errors.general && <div className={styles.error}>{errors.general}</div>}

      <form action={handleSubmit} className={styles.form}>
        <div className={styles.categoriesGrid}>
          {categories.map((category) => (
            <div key={category} className={styles.checkboxGroup}>
              <input type="checkbox" id={category} name="categories" value={category} className={styles.checkbox} />
              <label htmlFor={category} className={styles.checkboxLabel}>
                {category}
              </label>
            </div>
          ))}
        </div>

        <div className={styles.formGroup}>
          <label htmlFor="custom_category" className={styles.label}>
            Custom Category
          </label>
          <input
            type="text"
            id="custom_category"
            name="custom_category"
            className={styles.input}
            placeholder="Enter a custom category"
          />
        </div>

        <UpdateButton>Update Categories</UpdateButton>
      </form>
    </div>
  )
}

