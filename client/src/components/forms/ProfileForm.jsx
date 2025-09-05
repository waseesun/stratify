"use client"

import { useState } from "react"
import { useRouter } from "next/navigation"
import { updateUserAction } from "@/actions/userActions"
import {UpdateButton} from "@/components/buttons/Buttons"
import styles from "./ProfileForm.module.css"

export default function ProfileForm({ userId }) {
  const [errors, setErrors] = useState({})
  const [success, setSuccess] = useState("")
  const router = useRouter()

  const handleSubmit = async (formData) => {
    setErrors({})
    setSuccess("")

    try {
      const result = await updateUserAction(userId, formData)

      if (result.error) {
        setErrors(result.error)
      } else if (result.success) {
        setSuccess(result.success)
      }
    } catch (error) {
      setErrors({ general: "An unexpected error occurred" })
    }
  }

  return (
    <div className={styles.container}>
      <h2 className={styles.title}>Update Profile</h2>

      {success && <div className={styles.success}>{success}</div>}
      {errors.general && <div className={styles.error}>{errors.general}</div>}

      <form action={handleSubmit} className={styles.form}>
        <div className={styles.formGroup}>
          <label htmlFor="email" className={styles.label}>
            Email
          </label>
          <input type="email" id="email" name="email" className={styles.input} required />
          {errors.email && <span className={styles.fieldError}>{errors.email}</span>}
        </div>

        <div className={styles.formGroup}>
          <label htmlFor="username" className={styles.label}>
            Username
          </label>
          <input type="text" id="username" name="username" className={styles.input} />
          {errors.username && <span className={styles.fieldError}>{errors.username}</span>}
        </div>

        <div className={styles.formRow}>
          <div className={styles.formGroup}>
            <label htmlFor="first_name" className={styles.label}>
              First Name
            </label>
            <input type="text" id="first_name" name="first_name" className={styles.input} />
            {errors.first_name && <span className={styles.fieldError}>{errors.first_name}</span>}
          </div>

          <div className={styles.formGroup}>
            <label htmlFor="last_name" className={styles.label}>
              Last Name
            </label>
            <input type="text" id="last_name" name="last_name" className={styles.input} />
            {errors.last_name && <span className={styles.fieldError}>{errors.last_name}</span>}
          </div>
        </div>

        <div className={styles.formGroup}>
          <label htmlFor="address" className={styles.label}>
            Address
          </label>
          <input type="text" id="address" name="address" className={styles.input} />
          {errors.address && <span className={styles.fieldError}>{errors.address}</span>}
        </div>

        <div className={styles.formGroup}>
          <label htmlFor="description" className={styles.label}>
            Description
          </label>
          <textarea id="description" name="description" className={styles.textarea} rows="4" />
          {errors.description && <span className={styles.fieldError}>{errors.description}</span>}
        </div>

        <div className={styles.formGroup}>
          <label htmlFor="image_url" className={styles.label}>
            Profile Image URL
          </label>
          <input type="url" id="image_url" name="image_url" className={styles.input} />
          {errors.image_url && <span className={styles.fieldError}>{errors.image_url}</span>}
        </div>

        <div className={styles.formRow}>
          <div className={styles.formGroup}>
            <label htmlFor="password" className={styles.label}>
              Password
            </label>
            <input type="password" id="password" name="password" className={styles.input} required />
            {errors.password && <span className={styles.fieldError}>{errors.password}</span>}
          </div>

          <div className={styles.formGroup}>
            <label htmlFor="password_confirmation" className={styles.label}>
              Confirm Password
            </label>
            <input
              type="password"
              id="password_confirmation"
              name="password_confirmation"
              className={styles.input}
              required
            />
          </div>
        </div>

        <UpdateButton>Update Profile</UpdateButton>
      </form>
    </div>
  )
}

