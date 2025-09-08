"use client"

import { useState } from "react"
import { createTransactionAction } from "@/actions/transactionActions"
import CreateTransactionSubmitButton from "@/components/buttons/CreateTransactionSubmitButton"
import styles from "./TransactionForm.module.css"

export default function TransactionForm({ onSuccess, onSubmitting }) {
  const [errors, setErrors] = useState({})
  const [successMessage, setSuccessMessage] = useState("")

  const handleSubmit = async (e) => {
    e.preventDefault()
    setErrors({})
    setSuccessMessage("")
    onSubmitting(true)

    const formData = new FormData(e.target)

    try {
      const result = await createTransactionAction(formData)

      if (result.error) {
        if (typeof result.error === "object") {
          setErrors(result.error)
        } else {
          setErrors({ general: result.error })
        }
      } else if (result.success) {
        setSuccessMessage("Transaction created successfully!")
        setTimeout(() => {
          onSuccess()
        }, 1000)
      }
    } catch (error) {
      setErrors({ general: "An unexpected error occurred" })
    } finally {
      onSubmitting(false)
    }
  }

  return (
    <form onSubmit={handleSubmit} className={styles.form}>
      {errors.general && <div className={styles.error}>{errors.general}</div>}

      {successMessage && <div className={styles.success}>{successMessage}</div>}

      <div className={styles.field}>
        <label htmlFor="project" className={styles.label}>
          Project ID *
        </label>
        <input
          type="number"
          id="project"
          name="project"
          className={`${styles.input} ${errors.project ? styles.inputError : ""}`}
          required
          min="1"
        />
        {errors.project && <span className={styles.fieldError}>{errors.project}</span>}
      </div>

      <div className={styles.field}>
        <label htmlFor="milestone_name" className={styles.label}>
          Milestone Name *
        </label>
        <input
          type="text"
          id="milestone_name"
          name="milestone_name"
          className={`${styles.input} ${errors.milestone_name ? styles.inputError : ""}`}
          required
          maxLength="255"
        />
        {errors.milestone_name && <span className={styles.fieldError}>{errors.milestone_name}</span>}
      </div>

      <div className={styles.field}>
        <label htmlFor="amount" className={styles.label}>
          Amount *
        </label>
        <input
          type="number"
          id="amount"
          name="amount"
          className={`${styles.input} ${errors.amount ? styles.inputError : ""}`}
          required
          min="0"
          step="0.01"
        />
        {errors.amount && <span className={styles.fieldError}>{errors.amount}</span>}
      </div>

      <div className={styles.buttonContainer}>
        <CreateTransactionSubmitButton />
      </div>
    </form>
  )
}
