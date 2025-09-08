"use client"

import { useState } from "react"
import { useFormStatus } from "react-dom"
import { updateProposalAction } from "@/actions/proposalActions"
import styles from "./UpdateProposalForm.module.css"

function SubmitButton() {
  const { pending } = useFormStatus()

  return (
    <button type="submit" disabled={pending} className={`${styles.submitButton} ${pending ? styles.loading : ""}`}>
      {pending ? "Updating..." : "Update Proposal"}
    </button>
  )
}

export default function UpdateProposalForm({ proposal, onSuccess, onCancel }) {
  const [errors, setErrors] = useState({})
  const [successMessage, setSuccessMessage] = useState("")

  const handleSubmit = async (e) => {
    e.preventDefault()
    setErrors({})
    setSuccessMessage("")

    const formData = new FormData(e.target)

    try {
      const result = await updateProposalAction(proposal.id, formData)

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
        <label htmlFor="title" className={styles.label}>
          Title
        </label>
        <input type="text" id="title" name="title" defaultValue={proposal.title} className={styles.input} />
        {errors.title && <div className={styles.fieldError}>{errors.title}</div>}
      </div>

      <div className={styles.field}>
        <label htmlFor="description" className={styles.label}>
          Description
        </label>
        <textarea
          id="description"
          name="description"
          rows={4}
          defaultValue={proposal.description}
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
