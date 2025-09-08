"use client"

import { useFormStatus } from "react-dom"
import UpdateProjectSubmitButton from "@/components/buttons/UpdateProjectSubmitButton"
import styles from "./ProjectForm.module.css"

function SubmitButton({ isUpdate }) {
  const { pending } = useFormStatus()

  return <UpdateProjectSubmitButton pending={pending} text={isUpdate ? "Update Project" : "Create Project"} />
}

export default function ProjectForm({ onSubmit, errors = {}, initialData = {}, isUpdate = false }) {
  const handleSubmit = async (e) => {
    e.preventDefault()
    const formData = new FormData(e.target)
    await onSubmit(formData)
  }

  return (
    <form onSubmit={handleSubmit} className={styles.form}>
      <div className={styles.field}>
        <label htmlFor="fee" className={styles.label}>
          Fee *
        </label>
        <input
          type="number"
          id="fee"
          name="fee"
          defaultValue={initialData.fee || ""}
          className={`${styles.input} ${errors.fee ? styles.inputError : ""}`}
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
          defaultValue={initialData.start_date || ""}
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
          defaultValue={initialData.end_date || ""}
          className={`${styles.input} ${errors.end_date ? styles.inputError : ""}`}
          required
        />
        {errors.end_date && <span className={styles.errorText}>{errors.end_date}</span>}
      </div>

      {isUpdate && (
        <div className={styles.field}>
          <label htmlFor="status" className={styles.label}>
            Status
          </label>
          <select
            id="status"
            name="status"
            defaultValue={initialData.status || ""}
            className={`${styles.select} ${errors.status ? styles.inputError : ""}`}
          >
            <option value="">In Progress</option>
            <option value="completed">Completed</option>
            <option value="cancelled">Cancelled</option>
          </select>
          {errors.status && <span className={styles.errorText}>{errors.status}</span>}
        </div>
      )}

      <div className={styles.submitContainer}>
        <SubmitButton isUpdate={isUpdate} />
      </div>
    </form>
  )
}
