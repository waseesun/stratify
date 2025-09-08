"use client"

import ReviewForm from "../forms/ReviewForm"
import styles from "./UpdateReviewModal.module.css"

export default function UpdateReviewModal({ isOpen, onClose, review, onSuccess }) {
  if (!isOpen) return null

  return (
    <div className={styles.modalOverlay} onClick={onClose}>
      <div className={styles.modalContent} onClick={(e) => e.stopPropagation()}>
        <div className={styles.modalHeader}>
          <h2 className={styles.modalTitle}>Update Review</h2>
          <button className={styles.closeButton} onClick={onClose}>
            ×
          </button>
        </div>
        <ReviewForm existingReview={review} onSuccess={onSuccess} onCancel={onClose} />
      </div>
    </div>
  )
}
