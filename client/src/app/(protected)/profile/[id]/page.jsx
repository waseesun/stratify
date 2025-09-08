"use client"

import { useState, useEffect } from "react"
import { useParams, useRouter } from "next/navigation"
import { getUserAction } from "@/actions/userActions"
import { getReviewsAction } from "@/actions/reviewActions"
import { getUserIdAction } from "@/actions/authActions"
import ReviewCard from "@/components/cards/ReviewCard"
import CreateReviewButton from "@/components/buttons/CreateReviewButton"
import CreateReviewModal from "@/components/modals/CreateReviewModal"
import UpdateReviewModal from "@/components/modals/UpdateReviewModal"
import DeleteReviewModal from "@/components/modals/DeleteReviewModal"
import styles from "./page.module.css"
import Image from "next/image"

export default function ProfilePage() {
  const params = useParams()
  const router = useRouter()
  const [user, setUser] = useState(null)
  const [reviews, setReviews] = useState([])
  const [currentUserId, setCurrentUserId] = useState(null)
  const [error, setError] = useState(null)
  const [reviewsError, setReviewsError] = useState(null)
  const [loading, setLoading] = useState(true)
  const [reviewsLoading, setReviewsLoading] = useState(true)
  const [imageLoadFailed, setImageLoadFailed] = useState(false)

  // Modal states
  const [showCreateModal, setShowCreateModal] = useState(false)
  const [showUpdateModal, setShowUpdateModal] = useState(false)
  const [showDeleteModal, setShowDeleteModal] = useState(false)
  const [selectedReview, setSelectedReview] = useState(null)
  const [reviewToDelete, setReviewToDelete] = useState(null)

  useEffect(() => {
    const fetchUser = async () => {
      try {
        const result = await getUserAction(params.id)

        if (result.error) {
          setError(result.error)
          setUser(null)
        } else {
          setUser(result.data)
          setError(null)
        }
      } catch (error) {
        console.error("Failed to fetch user:", error)
        setError("An unexpected error occurred.")
        setUser(null)
      } finally {
        setLoading(false)
      }
    }

    if (params.id) {
      fetchUser()
    } else {
      setLoading(false)
      setError("User ID is missing.")
    }
  }, [params.id])

  useEffect(() => {
    const fetchReviewsAndUserId = async () => {
      try {
        const [reviewsResult, userIdResult] = await Promise.all([getReviewsAction(params.id), getUserIdAction()])

        console.log(reviewsResult)
        if (reviewsResult.error) {
          setReviewsError(reviewsResult.error)
          setReviews([])
        } else {
          setReviews(reviewsResult.data)
          setReviewsError(null)
        }

        setCurrentUserId(userIdResult)
      } catch (error) {
        console.error("Failed to fetch reviews:", error)
        setReviewsError("An unexpected error occurred.")
        setReviews([])
      } finally {
        setReviewsLoading(false)
      }
    }

    if (params.id) {
      fetchReviewsAndUserId()
    } else {
      setReviewsLoading(false)
    }
  }, [params.id])

  const handleSettingsClick = () => {
    router.push(`/profile/${params.id}/settings`)
  }

  const handleCreateReview = () => {
    setShowCreateModal(true)
  }

  const handleUpdateReview = (review) => {
    setSelectedReview(review)
    setShowUpdateModal(true)
  }

  const handleDeleteReview = (reviewId) => {
    setReviewToDelete(reviewId)
    setShowDeleteModal(true)
  }

  const handleReviewSuccess = () => {
    // Refresh reviews after successful create/update/delete
    const fetchReviews = async () => {
      try {
        const result = await getReviewsAction(params.id)
        if (result.error) {
          setReviewsError(result.error)
        } else {
          setReviews(result.data)
          setReviewsError(null)
        }
      } catch (error) {
        setReviewsError("Failed to refresh reviews.")
      }
    }

    fetchReviews()
    setShowCreateModal(false)
    setShowUpdateModal(false)
    setShowDeleteModal(false)
    setSelectedReview(null)
    setReviewToDelete(null)
  }

  if (loading) {
    return (
      <div className={styles.container}>
        <div className={styles.loading}>Loading...</div>
      </div>
    )
  }

  if (error) {
    return (
      <div className={styles.container}>
        <div className={styles.error}>{error}</div>
      </div>
    )
  }

  if (!user) {
    return (
      <div className={styles.container}>
        <div className={styles.error}>User not found.</div>
      </div>
    )
  }

  const { first_name, last_name, email, username, address, role, description, image_url, categories, portfolio_links } =
    user

  const getFullImageUrl = (url) => {
    if (url.startsWith("http")) {
      return url
    }
    return url ? `${process.env.NEXT_PUBLIC_BASE_URL}${url}` : "/path/to/default/image.jpg"
  }

  return (
    <div className={styles.container}>
      <div className={styles.profileCard}>
        <div className={styles.profileHeader}>
          <div className={styles.profileImageContainer}>
            {imageLoadFailed || !image_url ? (
              <div className={styles.fallbackImage}>
                {first_name ? first_name[0] : ""}
                {last_name ? last_name[0] : ""}
              </div>
            ) : (
              <Image
                src={getFullImageUrl(image_url) || "/placeholder.svg"}
                alt={`${username}'s profile picture`}
                width={150}
                height={150}
                className={styles.profileImage}
                onError={() => setImageLoadFailed(true)}
              />
            )}
          </div>
          <h1 className={styles.username}>{username}</h1>
          <p className={styles.email}>{email}</p>
        </div>

        <div className={styles.profileBody}>
          <div className={styles.section}>
            <h2 className={styles.sectionTitle}>Personal Information</h2>
            <div className={styles.infoGrid}>
              <div className={styles.infoItem}>
                <span className={styles.infoLabel}>Full Name:</span>
                <span className={styles.infoValue}>
                  {first_name || "N/A"} {last_name || ""}
                </span>
              </div>
              <div className={styles.infoItem}>
                <span className={styles.infoLabel}>Role:</span>
                <span className={styles.infoValue}>{role}</span>
              </div>
              <div className={styles.infoItem}>
                <span className={styles.infoLabel}>Address:</span>
                <span className={styles.infoValue}>{address || "N/A"}</span>
              </div>
            </div>
          </div>

          {description && (
            <div className={styles.section}>
              <h2 className={styles.sectionTitle}>Description</h2>
              <p className={styles.description}>{description}</p>
            </div>
          )}

          {categories && categories.length > 0 && (
            <div className={styles.section}>
              <h2 className={styles.sectionTitle}>Categories</h2>
              <div className={styles.tagsContainer}>
                {categories.map((category) => (
                  <span key={category.id} className={styles.tag}>
                    {category.name}
                  </span>
                ))}
              </div>
            </div>
          )}

          {portfolio_links && portfolio_links.length > 0 && (
            <div className={styles.section}>
              <h2 className={styles.sectionTitle}>Portfolio</h2>
              <ul className={styles.linkList}>
                {portfolio_links.map((link) => (
                  <li key={link.id} className={styles.linkItem}>
                    <a href={link.link} target="_blank" rel="noopener noreferrer" className={styles.link}>
                      {link.link}
                    </a>
                  </li>
                ))}
              </ul>
            </div>
          )}

          {currentUserId && currentUserId == params.id && (
            <button onClick={handleSettingsClick} className={styles.settingsButton}>
              Update Profile
            </button>
          )}

          <div className={styles.section}>
            <div className={styles.reviewsHeader}>
              <h2 className={styles.sectionTitle}>Reviews</h2>
              {currentUserId && currentUserId != params.id && (
                <CreateReviewButton onClick={handleCreateReview} />
              )}
            </div>

            {reviewsLoading ? (
              <div className={styles.reviewsLoading}>Loading reviews...</div>
            ) : reviewsError ? (
              <div className={styles.reviewsError}>{reviewsError}</div>
            ) : reviews.length === 0 ? (
              <div className={styles.noReviews}>No reviews yet.</div>
            ) : (
              <div className={styles.reviewsList}>
                {reviews.map((review) => (
                  <ReviewCard
                    key={review.id}
                    review={review}
                    currentUserId={currentUserId}
                    onUpdate={handleUpdateReview}
                    onDelete={handleDeleteReview}
                  />
                ))}
              </div>
            )}
          </div>
        </div>
      </div>

      <CreateReviewModal
        isOpen={showCreateModal}
        onClose={() => setShowCreateModal(false)}
        revieweeId={params.id}
        onSuccess={handleReviewSuccess}
      />

      <UpdateReviewModal
        isOpen={showUpdateModal}
        onClose={() => setShowUpdateModal(false)}
        review={selectedReview}
        onSuccess={handleReviewSuccess}
      />

      <DeleteReviewModal
        isOpen={showDeleteModal}
        onClose={() => setShowDeleteModal(false)}
        reviewId={reviewToDelete}
        onSuccess={handleReviewSuccess}
      />
    </div>
  )
}
