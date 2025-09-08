"use client"

import { useState, useEffect } from "react"
import { deleteProposalAction } from "@/actions/proposalActions"
import styles from "./DeleteProposalModal.module.css"

export default function DeleteProposalModal({ proposalId, onClose, onSuccess }) {
  const [loading, setLoading] = useState(false)
  const [error, setError] = useState("")

  useEffect(() => {
    const handleEscape = (e) => {
      if (e.key === "Escape") {
        onClose()
      }
    }

    document.addEventListener("keydown", handleEscape)
    document.body.style.overflow = "hidden"

    return () => {
      document.removeEventListener("keydown", handleEscape)
      document.body.style.overflow = "unset"
    }
  }, [onClose])

  const handleBackdropClick = (e) => {
    if (e.target === e.currentTarget) {
      onClose()
    }
  }

  const handleDelete = async () => {
    setLoading(true)
    setError("")

    try {
      const result = await deleteProposalAction(proposalId)

      if (result.error) {
        setError(result.error)
      } else if (result.success) {
        onSuccess()
      }
    } catch (err) {
      setError("An unexpected error occurred")
    } finally {
      setLoading(false)
    }
  }

  return (
    <div className={styles.overlay} onClick={handleBackdropClick}>
      <div className={styles.modal}>
        <div className={styles.header}>
          <h2 className={styles.title}>Delete Proposal</h2>
          <button className={styles.closeButton} onClick={onClose}>
            ×
          </button>
        </div>

        <div className={styles.content}>
          {error && <div className={styles.error}>{typeof error === "object" ? JSON.stringify(error) : error}</div>}

          <p className={styles.message}>Are you sure you want to delete this proposal? This action cannot be undone.</p>

          <div className={styles.actions}>
            <button type="button" onClick={onClose} className={styles.cancelButton} disabled={loading}>
              No, Cancel
            </button>
            <button type="button" onClick={handleDelete} className={styles.deleteButton} disabled={loading}>
              {loading ? "Deleting..." : "Yes, Delete"}
            </button>
          </div>
        </div>
      </div>
    </div>
  )
}
