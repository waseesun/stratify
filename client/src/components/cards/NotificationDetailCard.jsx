"use client"

import { useState } from "react"
import { useRouter } from "next/navigation"
import DeleteNotificationButton from "@/components/buttons/DeleteNotificationButton"
import DeleteNotificationModal from "@/components/modals/DeleteNotificationModal"
import styles from "./NotificationDetailCard.module.css"

export default function NotificationDetailCard({ notification }) {
  const router = useRouter()
  const [showDeleteModal, setShowDeleteModal] = useState(false)

  const formatDate = (dateString) => {
    const date = new Date(dateString)
    return date.toLocaleDateString("en-US", {
      year: "numeric",
      month: "long",
      day: "numeric",
      hour: "2-digit",
      minute: "2-digit",
    })
  }

  const getTypeIcon = (type) => {
    switch (type) {
      case "problem":
        return "📋"
      case "transaction":
        return "💰"
      case "project":
        return "🚀"
      default:
        return "📢"
    }
  }

  const handleLinkClick = () => {
    if (notification.link) {
      router.push(notification.link)
    }
  }

  const handleDeleteSuccess = () => {
    router.push("/notifications")
  }

  return (
    <>
      <div className={styles.card}>
        <div className={styles.header}>
          <button className={styles.backButton} onClick={() => router.push("/notifications")}>
            ← Back to Notifications
          </button>
          <DeleteNotificationButton onClick={() => setShowDeleteModal(true)} />
        </div>

        <div className={styles.content}>
          <div className={styles.typeSection}>
            <div className={styles.typeIcon}>{getTypeIcon(notification.type)}</div>
            <div className={styles.typeInfo}>
              <h2 className={styles.type}>
                {notification.type.charAt(0).toUpperCase() + notification.type.slice(1)} Notification
              </h2>
              <div className={styles.status}>
                {notification.is_read ? (
                  <span className={styles.readStatus}>Read</span>
                ) : (
                  <span className={styles.unreadStatus}>Unread</span>
                )}
              </div>
            </div>
          </div>

          <div className={styles.messageSection}>
            <h3 className={styles.messageTitle}>Message</h3>
            <p className={styles.message}>{notification.message}</p>
          </div>

          <div className={styles.detailsSection}>
            <div className={styles.detailItem}>
              <span className={styles.detailLabel}>Created:</span>
              <span className={styles.detailValue}>{formatDate(notification.created_at)}</span>
            </div>

            {notification.updated_at && (
              <div className={styles.detailItem}>
                <span className={styles.detailLabel}>Updated:</span>
                <span className={styles.detailValue}>{formatDate(notification.updated_at)}</span>
              </div>
            )}

            <div className={styles.detailItem}>
              <span className={styles.detailLabel}>Type:</span>
              <span className={styles.detailValue}>{notification.type}</span>
            </div>
          </div>

          {notification.link && (
            <div className={styles.actionSection}>
              <button className={styles.linkButton} onClick={handleLinkClick}>
                View Related Content →
              </button>
            </div>
          )}
        </div>
      </div>

      {showDeleteModal && (
        <DeleteNotificationModal
          notificationId={notification.id}
          onClose={() => setShowDeleteModal(false)}
          onSuccess={handleDeleteSuccess}
        />
      )}
    </>
  )
}
