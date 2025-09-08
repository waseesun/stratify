"use client"

import { useState, useEffect } from "react"
import { useRouter, useSearchParams } from "next/navigation"
import { getCompanyProblemsAction } from "@/actions/problemActions"
import ProblemCard from "@/components/cards/ProblemCard"
import {CreateProblemButton} from "@/components/buttons/Buttons"
import CreateProblemModal from "@/components/modals/CreateProblemModal"
import Pagination from "@/components/pagination/Pagination"
import styles from "./page.module.css"

export default function ProblemsPage() {
  const [problems, setProblems] = useState([])
  const [loading, setLoading] = useState(true)
  const [error, setError] = useState("")
  const [showCreateModal, setShowCreateModal] = useState(false)
  const [pagination, setPagination] = useState(null)

  const router = useRouter()
  const searchParams = useSearchParams()
  const currentPage = Number.parseInt(searchParams.get("page")) || 1

  const fetchProblems = async (queryParams = {}) => {
    setLoading(true)
    setError("")

    const params = {
      page: currentPage,
      ...queryParams,
    }

    const result = await getCompanyProblemsAction(params)

    if (result.error) {
      setError(typeof result.error === "string" ? result.error : "Failed to fetch problems")
    } else {
      setProblems(result.data || [])
      setPagination(result.pagination)
    }

    setLoading(false)
  }

  useEffect(() => {
    fetchProblems()
  }, [currentPage])

  const handlePageChange = (page) => {
    router.push(`/problems?page=${page}`)
  }

  const handleProblemCreated = () => {
    setShowCreateModal(false)
    fetchProblems() // Refresh the list
  }

  return (
    <div className={styles.container}>
      <div className={styles.header}>
        <h1 className={styles.title}>My Problems</h1>
        <CreateProblemButton onClick={() => setShowCreateModal(true)} />
      </div>

      {error && <div className={styles.error}>{error}</div>}

      {loading ? (
        <div className={styles.loading}>Loading problems...</div>
      ) : (
        <>
          <div className={styles.problemsGrid}>
            {problems.length > 0 ? (
              problems.map((problem) => <ProblemCard key={problem.id} problem={problem} />)
            ) : (
              <div className={styles.noproblems}>No problems found</div>
            )}
          </div>

          {pagination && pagination.total_pages > 1 && (
            <Pagination currentPage={currentPage} totalPages={pagination.total_pages} onPageChange={handlePageChange} />
          )}
        </>
      )}

      {showCreateModal && (
        <CreateProblemModal onClose={() => setShowCreateModal(false)} onSuccess={handleProblemCreated} />
      )}
    </div>
  )
}
