"use client"

import { useState, useEffect } from "react"
import { useRouter, useSearchParams } from "next/navigation"
import { getProblemsAction } from "@/actions/problemActions"
import ProblemCard from "@/components/cards/ProblemCard"
import Pagination from "@/components/pagination/Pagination"
import styles from "./page.module.css"

export default function ProblemsPage() {
  const [problems, setProblems] = useState([])
  const [loading, setLoading] = useState(true)
  const [error, setError] = useState("")
  const [searchTitle, setSearchTitle] = useState("")
  const [statusFilter, setStatusFilter] = useState("")
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

    if (searchTitle) params.title = searchTitle
    if (statusFilter) params.status = statusFilter

    const result = await getProblemsAction(params)

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
  }, [currentPage, searchTitle, statusFilter])

  const handleSearch = (e) => {
    e.preventDefault()
    const formData = new FormData(e.target)
    const title = formData.get("title")
    setSearchTitle(title)

    // Reset to page 1 when searching
    if (currentPage !== 1) {
      router.push("/problems?page=1")
    }
  }

  const handleStatusFilter = (e) => {
    setStatusFilter(e.target.value)

    // Reset to page 1 when filtering
    if (currentPage !== 1) {
      router.push("/problems?page=1")
    }
  }

  const handlePageChange = (page) => {
    router.push(`/problems?page=${page}`)
  }

  return (
    <div className={styles.container}>
      <div className={styles.header}>
        <h1 className={styles.title}>Problems</h1>
      </div>

      <div className={styles.filters}>
        <form onSubmit={handleSearch} className={styles.searchForm}>
          <input
            type="text"
            name="title"
            placeholder="Search problems by title..."
            className={styles.searchInput}
            defaultValue={searchTitle}
          />
          <button type="submit" className={styles.searchButton}>
            Search
          </button>
        </form>

        <select value={statusFilter} onChange={handleStatusFilter} className={styles.statusFilter}>
          <option value="">All Status</option>
          <option value="open">Open</option>
          <option value="sold">Sold</option>
          <option value="cancelled">Cancelled</option>
        </select>
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
    </div>
  )
}
