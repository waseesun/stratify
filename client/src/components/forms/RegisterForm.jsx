"use client"

import { useState } from "react"
import { useRouter } from "next/navigation"
import { createUserAction } from "@/actions/userActions"
import {RegisterButton} from "@/components/buttons/Buttons"
import styles from "./RegisterForm.module.css"

export default function RegisterForm({ userType, onBack }) {
  const [errors, setErrors] = useState({})
  const [successMessage, setSuccessMessage] = useState("")
  const router = useRouter()

  const handleSubmit = async (formData) => {
    setErrors({})
    setSuccessMessage("")

    const result = await createUserAction(formData, userType)

    if (result.error) {
      setErrors(result.error)
    } else if (result.success) {
      setSuccessMessage("Registered successfully")
      setTimeout(() => {
        router.push("/auth/login")
      }, 2000)
    }
  }

  return (
    <div className={styles.container}>
      <form action={handleSubmit} className={styles.form}>
        <button type="button" onClick={onBack} className={styles.backButton}>
          ⬅ Back
        </button>
        <div className={styles.header}>
          <h2 className={styles.title}>Create {userType === "company" ? "Company" : "Freelancer"} Account</h2>
        </div>

        {successMessage && <div className={styles.successMessage}>{successMessage}</div>}

        <div className={styles.inputGroup}>
          <label htmlFor="email" className={styles.label}>
            Email *
          </label>
          <input type="email" id="email" name="email" className={styles.input} required />
          {errors.email && <span className={styles.error}>{errors.email}</span>}
        </div>

        <div className={styles.inputGroup}>
          <label htmlFor="username" className={styles.label}>
            Username *
          </label>
          <input type="text" id="username" name="username" className={styles.input} required />
          {errors.username && <span className={styles.error}>{errors.username}</span>}
        </div>

        <div className={styles.row}>
          <div className={styles.inputGroup}>
            <label htmlFor="first_name" className={styles.label}>
              First Name
            </label>
            <input type="text" id="first_name" name="first_name" className={styles.input} />
            {errors.first_name && <span className={styles.error}>{errors.first_name}</span>}
          </div>

          <div className={styles.inputGroup}>
            <label htmlFor="last_name" className={styles.label}>
              Last Name
            </label>
            <input type="text" id="last_name" name="last_name" className={styles.input} />
            {errors.last_name && <span className={styles.error}>{errors.last_name}</span>}
          </div>
        </div>

        <div className={styles.inputGroup}>
          <label htmlFor="address" className={styles.label}>
            Address
          </label>
          <input type="text" id="address" name="address" className={styles.input} />
          {errors.address && <span className={styles.error}>{errors.address}</span>}
        </div>

        <div className={styles.inputGroup}>
          <label htmlFor="description" className={styles.label}>
            Describe yourself
          </label>
          <textarea id="description" name="description" className={styles.textarea} rows="3" />
          {errors.description && <span className={styles.error}>{errors.description}</span>}
        </div>

        <div className={styles.inputGroup}>
          <label htmlFor="password" className={styles.label}>
            Password *
          </label>
          <input type="password" id="password" name="password" className={styles.input} required />
          {errors.password && <span className={styles.error}>{errors.password}</span>}
        </div>

        <div className={styles.inputGroup}>
          <label htmlFor="password_confirmation" className={styles.label}>
            Confirm Password *
          </label>
          <input
            type="password"
            id="password_confirmation"
            name="password_confirmation"
            className={styles.input}
            required
          />
          {errors.password_confirmation && <span className={styles.error}>{errors.password_confirmation}</span>}
        </div>

        {errors.error && <div className={styles.errorMessage}>{errors.error}</div>}

        <RegisterButton />

        <div className={styles.loginLinkContainer}>
          Already have an account? <a href="/auth/login" className={styles.loginLink}>Login</a>
        </div>
      </form>
    </div>
  )
}
