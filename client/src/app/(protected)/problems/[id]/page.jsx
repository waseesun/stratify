"use client"

import { useState, useEffect } from "react"
import { useRouter, useParams } from "next/navigation"
import { getProblemAction } from "@/actions/problemActions"
import { getUserIdAction } from "@/actions/authActions"
import {UpdateProblemButton, DeleteProblemButton} from "@/components/buttons/Buttons"
import ProblemDetailCard from "@/components/cards/ProblemDetailCard"
import UpdateProblemModal from "@/components/modals/UpdateProblemModal"
import DeleteProblemModal from "@/components/modals/DeleteProblemModal"
import styles from "./page.module.css"

export default function ProblemDetailPage() {
  const params = useParams()
  const [problem, setProblem] = useState(null)
  const [loading, setLoading] = useState(true)
  const [error, setError] = useState("")
  const [showUpdateModal, setShowUpdateModal] = useState(false)
  const [showDeleteModal, setShowDeleteModal] = useState(false)
  const [userId, setUserId] = useState(null)

  const router = useRouter()
  const problemId = params.id

  const fetchUserID = async () => {
    const userId = await getUserIdAction();

    if (userId) {
      setUserId(userId);
    } else {
      router.push("/auth/login");
    }
  }

  const fetchProblem = async () => {
    setLoading(true)
    setError("")

    const result = await getProblemAction(problemId)

    if (result.error) {
      setError(typeof result.error === "string" ? result.error : "Failed to fetch problem")
    } else {
      setProblem(result.data)
    }

    setLoading(false)
  }

  useEffect(() => {
    fetchUserID()
    fetchProblem()
  }, [problemId])

  const handleUpdateSuccess = () => {
    setShowUpdateModal(false)
    fetchProblem() // Refresh the problem data
  }

  const handleDeleteSuccess = () => {
    setShowDeleteModal(false)
    router.push("/problems")
  }

  if (loading) {
    return (
      <div className={styles.container}>
        <div className={styles.loading}>Loading problem...</div>
      </div>
    )
  }

  if (error) {
    return (
      <div className={styles.container}>
        <div className={styles.error}>{error}</div>
        <button className={styles.backButton} onClick={() => router.push("/problems")}>
          Back to Problems
        </button>
      </div>
    )
  }

  if (!problem) {
    return (
      <div className={styles.container}>
        <div className={styles.error}>Problem not found</div>
        <button className={styles.backButton} onClick={() => router.push("/problems")}>
          Back to Problems
        </button>
      </div>
    )
  }

  return (
    <div className={styles.container}>
      <div className={styles.header}>
        <button className={styles.backButton} onClick={() => router.push("/problems")}>
          ← Back to Problems
        </button>

        {userId == problem.company_id && (
          <div className={styles.actions}>
            <UpdateProblemButton onClick={() => setShowUpdateModal(true)} />
            <DeleteProblemButton onClick={() => setShowDeleteModal(true)} />
          </div>
        )}
      </div>

      <ProblemDetailCard problem={problem} />

      {showUpdateModal && (
        <UpdateProblemModal
          problem={problem}
          onClose={() => setShowUpdateModal(false)}
          onSuccess={handleUpdateSuccess}
        />
      )}

      {showDeleteModal && (
        <DeleteProblemModal
          problemId={problem.id}
          problemTitle={problem.title}
          onClose={() => setShowDeleteModal(false)}
          onSuccess={handleDeleteSuccess}
        />
      )}
    </div>
  )
}
