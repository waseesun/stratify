"use client"

import { useFormStatus } from "react-dom"
import AcceptProposalSubmitButton from "@/components/buttons/AcceptProposalSubmitButton"
import styles from "./AcceptProposalForm.module.css"

function SubmitButton() {
  const { pending } = useFormStatus()

  return <AcceptProposalSubmitButton pending={pending} />
}

export default function AcceptProposalForm({ onSubmit, errors = {} }) {
  const handleSubmit = async (e) => {
    e.preventDefault()
    const formData = new FormData(e.target)
    await onSubmit(formData)
  }

  return (
    <form onSubmit={handleSubmit} className={styles.form}>
      <div className={styles.field}>
        <label htmlFor="fee" className={styles.label}>
          Project Fee *
        </label>
        <input
          type="number"
          id="fee"
          name="fee"
          className={`${styles.input} ${errors.fee ? styles.inputError : ""}`}
          placeholder="Enter project fee"
          required
        />
        {errors.fee && <span className={styles.errorText}>{errors.fee}</span>}
      </div>

      <div className={styles.field}>
        <label htmlFor="start_date" className={styles.label}>
          Start Date *
        </label>
        <input
          type="date"
          id="start_date"
          name="start_date"
          className={`${styles.input} ${errors.start_date ? styles.inputError : ""}`}
          required
        />
        {errors.start_date && <span className={styles.errorText}>{errors.start_date}</span>}
      </div>

      <div className={styles.field}>
        <label htmlFor="end_date" className={styles.label}>
          End Date *
        </label>
        <input
          type="date"
          id="end_date"
          name="end_date"
          className={`${styles.input} ${errors.end_date ? styles.inputError : ""}`}
          required
        />
        {errors.end_date && <span className={styles.errorText}>{errors.end_date}</span>}
      </div>

      <div className={styles.submitContainer}>
        <SubmitButton />
      </div>
    </form>
  )
}
