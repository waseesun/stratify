"use client"

import { useState, useEffect } from "react"
import { useFormStatus } from "react-dom"
import { getAllProblemsAction } from "@/actions/problemActions"
import { getUserIdAction } from "@/actions/authActions"
import { createProposalAction } from "@/actions/proposalActions"
import styles from "./ProposalForm.module.css"

function SubmitButton() {
  const { pending } = useFormStatus()

  return (
    <button type="submit" disabled={pending} className={`${styles.submitButton} ${pending ? styles.loading : ""}`}>
      {pending ? "Creating..." : "Create Proposal"}
    </button>
  )
}

export default function ProposalForm({ onSuccess, onCancel, initialData = null }) {
  const [errors, setErrors] = useState({})
  const [successMessage, setSuccessMessage] = useState("")
  const [userId, setUserId] = useState(null);
  const [problems, setProblems] = useState([])
  
  const fetchUserID = async () => {
    const userId = await getUserIdAction();
    console.log(userId)

    if (userId) {
      setUserId(userId);
    }
  }

  const fetchProblems = async () => {
    setProblems([])

    try {
      const result = await getAllProblemsAction()

      if (result.error) {
        console.error("Error fetching problems:", result.error)
      } else {
        setProblems(result.data)
      }
    } catch (error) {
      console.error("Error fetching problems:", error)
    }
  }

  useEffect(() => {
    fetchProblems();
    fetchUserID();
  }, [])

  const handleSubmit = async (e) => {
    e.preventDefault()
    setErrors({})
    setSuccessMessage("")

    const formData = new FormData(e.target)
    formData.append("provider", userId);

    try {
      const result = await createProposalAction(formData)

      if (result.error) {
        setErrors(result.error)
      } else if (result.success) {
        setSuccessMessage(result.success)
        setTimeout(() => {
          onSuccess()
        }, 1000)
      }
    } catch (error) {
      setErrors({ error: "An unexpected error occurred" })
    }
  }

  return (
    <form onSubmit={handleSubmit} className={styles.form}>
      {successMessage && <div className={styles.success}>{successMessage}</div>}

      {errors.error && (
        <div className={styles.error}>
          {typeof errors.error === "object" ? JSON.stringify(errors.error) : errors.error}
        </div>
      )}

      <div className={styles.field}>
        <label htmlFor="problem" className={styles.label}>Problem</label>
        <select id="problem" name="problem" className={styles.select}>
          <option value="">All Problems</option>
          {problems.map((prob) => (
            <option key={prob.id} value={prob.id}>
              {prob.title}
            </option>
          ))}
        </select>
      </div>

      <div className={styles.field}>
        <label htmlFor="title" className={styles.label}>
          Title *
        </label>
        <input
          type="text"
          id="title"
          name="title"
          required
          defaultValue={initialData?.title || ""}
          className={styles.input}
        />
        {errors.title && <div className={styles.fieldError}>{errors.title}</div>}
      </div>

      <div className={styles.field}>
        <label htmlFor="description" className={styles.label}>
          Description *
        </label>
        <textarea
          id="description"
          name="description"
          required
          rows={4}
          defaultValue={initialData?.description || ""}
          className={styles.textarea}
        />
        {errors.description && <div className={styles.fieldError}>{errors.description}</div>}
      </div>

      <div className={styles.actions}>
        <button type="button" onClick={onCancel} className={styles.cancelButton}>
          Cancel
        </button>
        <SubmitButton />
      </div>
    </form>
  )
}
