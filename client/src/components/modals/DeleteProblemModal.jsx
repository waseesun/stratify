"use client"

import { useState } from "react"
import { deleteProblemAction } from "@/actions/problemActions"
import styles from "./DeleteProblemModal.module.css"

export default function DeleteProblemModal({ problemId, problemTitle, onClose, onSuccess }) {
  const [loading, setLoading] = useState(false)
  const [error, setError] = useState("")

  const handleDelete = async () => {
    setLoading(true)
    setError("")

    const result = await deleteProblemAction(problemId)

    if (result.error) {
      setError(typeof result.error === "string" ? result.error : "Failed to delete problem")
      setLoading(false)
    } else {
      onSuccess()
    }
  }

  return (
    <div className={styles.overlay} onClick={onClose}>
      <div className={styles.modal} onClick={(e) => e.stopPropagation()}>
        <div className={styles.header}>
          <h2 className={styles.title}>Delete Problem</h2>
          <button className={styles.closeButton} onClick={onClose}>
            ×
          </button>
        </div>

        {error && <div className={styles.error}>{error}</div>}

        <div className={styles.content}>
          <p className={styles.message}>Are you sure you want to delete the problem "{problemTitle}"?</p>
          <p className={styles.warning}>This action cannot be undone.</p>
        </div>

        <div className={styles.actions}>
          <button className={styles.cancelButton} onClick={onClose} disabled={loading}>
            Cancel
          </button>
          <button className={styles.deleteButton} onClick={handleDelete} disabled={loading}>
            {loading ? "Deleting..." : "Yes, Delete"}
          </button>
        </div>
      </div>
    </div>
  )
}
