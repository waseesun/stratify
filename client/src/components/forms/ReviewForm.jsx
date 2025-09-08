"use client"

import { useState } from "react"
import { createReviewAction, updateReviewAction } from "@/actions/reviewActions"
import CreateReviewSubmitButton from "../buttons/CreateReviewSubmitButton"
import UpdateReviewSubmitButton from "../buttons/UpdateReviewSubmitButton"
import styles from "./ReviewForm.module.css"

export default function ReviewForm({ revieweeId, existingReview = null, onSuccess, onCancel }) {
  const [rating, setRating] = useState(existingReview?.rating || 1)
  const [comment, setComment] = useState(existingReview?.comment || "")
  const [errors, setErrors] = useState({})
  const [successMessage, setSuccessMessage] = useState("")

  const isUpdate = !!existingReview

  const handleSubmit = async (e) => {
    e.preventDefault()
    setErrors({})
    setSuccessMessage("")

    const formData = new FormData()
    if (!isUpdate) {
      formData.append("reviewee_id", revieweeId)
    }
    formData.append("rating", rating)
    formData.append("comment", comment)

    try {
      const result = isUpdate
        ? await updateReviewAction(existingReview.id, formData)
        : await createReviewAction(formData)

      if (result.error) {
        setErrors(result.error)
      } else if (result.success) {
        setSuccessMessage(result.success)
        setTimeout(() => {
          onSuccess()
        }, 1500)
      }
    } catch (error) {
      setErrors({ error: "An unexpected error occurred." })
    }
  }

  const renderStars = () => {
    return Array.from({ length: 5 }, (_, index) => (
      <button
        key={index}
        type="button"
        className={`${styles.starButton} ${index < rating ? styles.filled : styles.empty}`}
        onClick={() => setRating(index + 1)}
      >
        ★
      </button>
    ))
  }

  return (
    <form onSubmit={handleSubmit} className={styles.form}>
      {errors.error && <div className={styles.errorMessage}>{errors.error}</div>}

      {successMessage && <div className={styles.successMessage}>{successMessage}</div>}

      <div className={styles.formGroup}>
        <label className={styles.label}>Rating</label>
        <div className={styles.starsContainer}>
          {renderStars()}
          <span className={styles.ratingText}>({rating}/5)</span>
        </div>
        {errors.rating && <div className={styles.fieldError}>{errors.rating}</div>}
      </div>

      <div className={styles.formGroup}>
        <label htmlFor="comment" className={styles.label}>
          Comment
        </label>
        <textarea
          id="comment"
          value={comment}
          onChange={(e) => setComment(e.target.value)}
          className={styles.textarea}
          rows={4}
          placeholder="Write your review..."
          required
        />
        {errors.comment && <div className={styles.fieldError}>{errors.comment}</div>}
      </div>

      <div className={styles.formActions}>
        <button type="button" onClick={onCancel} className={styles.cancelButton}>
          Cancel
        </button>
        {isUpdate ? <UpdateReviewSubmitButton /> : <CreateReviewSubmitButton />}
      </div>
    </form>
  )
}
