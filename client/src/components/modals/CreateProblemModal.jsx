"use client"

import { useState } from "react"
import { createProblemAction } from "@/actions/problemActions"
import ProblemForm from "@/components/forms/ProblemForm"
import styles from "./CreateProblemModal.module.css"

export default function CreateProblemModal({ onClose, onSuccess }) {
  const [error, setError] = useState("")
  const [success, setSuccess] = useState("")

  const handleSubmit = async (formData) => {
    setError("")
    setSuccess("")

    const result = await createProblemAction(formData)

    if (result.error) {
      if (typeof result.error === "object") {
        const errorMessages = Object.values(result.error).flat().join(", ")
        setError(errorMessages)
      } else {
        setError(result.error)
      }
    } else {
      setSuccess("Problem created successfully!")
      setTimeout(() => {
        onSuccess()
      }, 1500)
    }
  }

  return (
    <div className={styles.overlay} onClick={onClose}>
      <div className={styles.modal} onClick={(e) => e.stopPropagation()}>
        <div className={styles.header}>
          <h2 className={styles.title}>Create New Problem</h2>
          <button className={styles.closeButton} onClick={onClose}>
            ×
          </button>
        </div>

        {error && <div className={styles.error}>{error}</div>}
        {success && <div className={styles.success}>{success}</div>}

        <ProblemForm onSubmit={handleSubmit} />
      </div>
    </div>
  )
}
