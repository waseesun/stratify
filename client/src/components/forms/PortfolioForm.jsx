"use client"

import { useState } from "react"
import { updateUserPortfolioLinksAction } from "@/actions/userActions"
import {UpdateButton} from "@/components/buttons/Buttons"
import styles from "./PortfolioForm.module.css"

export default function PortfolioForm({ userId }) {
  const [errors, setErrors] = useState({})
  const [success, setSuccess] = useState("")

  const handleSubmit = async (formData) => {
    setErrors({})
    setSuccess("")

    try {
      const result = await updateUserPortfolioLinksAction(userId, formData)

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
      <h2 className={styles.title}>Update Portfolio Links</h2>

      {success && <div className={styles.success}>{success}</div>}
      {errors.general && <div className={styles.error}>{errors.general}</div>}

      <form action={handleSubmit} className={styles.form}>
        <div className={styles.formGroup}>
          <label htmlFor="github" className={styles.label}>
            GitHub URL
          </label>
          <input
            type="url"
            id="github"
            name="github"
            className={styles.input}
            placeholder="https://github.com/username"
          />
        </div>

        <div className={styles.formGroup}>
          <label htmlFor="linkedin" className={styles.label}>
            LinkedIn URL
          </label>
          <input
            type="url"
            id="linkedin"
            name="linkedin"
            className={styles.input}
            placeholder="https://linkedin.com/in/username"
          />
        </div>

        <div className={styles.formGroup}>
          <label htmlFor="portfolio" className={styles.label}>
            Portfolio Website
          </label>
          <input
            type="url"
            id="portfolio"
            name="portfolio"
            className={styles.input}
            placeholder="https://yourportfolio.com"
          />
        </div>

        <div className={styles.formGroup}>
          <label htmlFor="behance" className={styles.label}>
            Behance URL
          </label>
          <input
            type="url"
            id="behance"
            name="behance"
            className={styles.input}
            placeholder="https://behance.net/username"
          />
        </div>

        <div className={styles.formGroup}>
          <label htmlFor="dribbble" className={styles.label}>
            Dribbble URL
          </label>
          <input
            type="url"
            id="dribbble"
            name="dribbble"
            className={styles.input}
            placeholder="https://dribbble.com/username"
          />
        </div>

        <UpdateButton>Update Portfolio Links</UpdateButton>
      </form>
    </div>
  )
}
