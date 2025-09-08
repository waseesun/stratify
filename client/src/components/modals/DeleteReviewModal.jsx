"use client"

import { useState } from "react"
import { deleteReviewAction } from "@/actions/reviewActions"
import styles from "./DeleteReviewModal.module.css"

export default function DeleteReviewModal({ isOpen, onClose, reviewId, onSuccess }) {
  const [isDeleting, setIsDeleting] = useState(false)
  const [error, setError] = useState("")

  if (!isOpen) return null

  const handleDelete = async () => {
    setIsDeleting(true)
    setError("")

    try {
      const result = await deleteReviewAction(reviewId)

      if (result.error) {
        setError(result.error)
      } else if (result.success) {
        onSuccess()
      }
    } catch (error) {
      setError("An unexpected error occurred.")
    } finally {
      setIsDeleting(false)
    }
  }

  return (
    <div className={styles.modalOverlay} onClick={onClose}>
      <div className={styles.modalContent} onClick={(e) => e.stopPropagation()}>
        <div className={styles.modalHeader}>
          <h2 className={styles.modalTitle}>Delete Review</h2>
          <button className={styles.closeButton} onClick={onClose}>
            ×
          </button>
        </div>

        <div className={styles.modalBody}>
          <p className={styles.confirmationText}>
            Are you sure you want to delete this review? This action cannot be undone.
          </p>

          {error && <div className={styles.errorMessage}>{error}</div>}
        </div>

        <div className={styles.modalActions}>
          <button className={styles.cancelButton} onClick={onClose} disabled={isDeleting}>
            No, Cancel
          </button>
          <button className={styles.deleteButton} onClick={handleDelete} disabled={isDeleting}>
            {isDeleting ? "Deleting..." : "Yes, Delete"}
          </button>
        </div>
      </div>
    </div>
  )
}
