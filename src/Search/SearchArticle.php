<?php
namespace App\Search;

class SearchArticle
{
    // création de 3 propriétés qui correspondent aux critères de recherche possibles
    public function __construct(
        private ?string $title = null,
        private ?array $tags = [],
        private ?array $authors = [],
        private int $page = 1,
    ) {
    }

        /**
         * Get the value of title
         *
         * @return ?string
         */
        public function getTitle(): ?string
        {
                return $this->title;
        }

        /**
         * Set the value of title
         *
         * @param ?string $title
         *
         * @return self
         */
        public function setTitle(?string $title): self
        {
                $this->title = $title;

                return $this;
        }

        /**
         * Get the value of tags
         *
         * @return ?array
         */
        public function getTags(): ?array
        {
                return $this->tags;
        }

        /**
         * Set the value of tags
         *
         * @param ?array $tags
         *
         * @return self
         */
        public function setTags(?array $tags): self
        {
                $this->tags = $tags;

                return $this;
        }

        /**
         * Get the value of authors
         *
         * @return ?array
         */
        public function getAuthors(): ?array
        {
                return $this->authors;
        }

        /**
         * Set the value of authors
         *
         * @param ?array $authors
         *
         * @return self
         */
        public function setAuthors(?array $authors): self
        {
                $this->authors = $authors;

                return $this;
        }

        /**
         * Get the value of page
         *
         * @return int
         */
        public function getPage(): int
        {
                return $this->page;
        }

        /**
         * Set the value of page
         *
         * @param int $page
         *
         * @return self
         */
        public function setPage(int $page): self
        {
                $this->page = $page;

                return $this;
        }
}