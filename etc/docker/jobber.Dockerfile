FROM arm32v7/golang:1.13-alpine

ENV JOBBER_VER v1.3.1
ENV SRC_HASH 8d8cdeb941710e168f8f63abbfc06aab2aadfdfc22b3f6de7108f56403860476

RUN apk update && \
    apk upgrade && \
    apk add --update make gcc libc-dev rsync grep ca-certificates openssl wget

WORKDIR /go_wkspc/src/github.com/dshearer
RUN wget "https://api.github.com/repos/dshearer/jobber/tarball/${JOBBER_VER}" -O jobber.tar.gz && \
    echo "${SRC_HASH}  jobber.tar.gz" | sha256sum -cw && \
    tar xzf *.tar.gz && rm *.tar.gz && mv dshearer-* jobber && \
    cd jobber && \
    make check && \
    make install DESTDIR=/jobber-dist/

FROM arm32v7/alpine:3.10.3

RUN mkdir /jobber
COPY --from=0 /jobber-dist/usr/local/libexec/jobberrunner /jobber/jobberrunner
COPY --from=0 /jobber-dist/usr/local/bin/jobber /jobber/jobber
ENV PATH /jobber:${PATH}

ENV USERID 100
RUN addgroup jobberuser && \
    adduser -S -u "${USERID}" -G jobberuser jobberuser && \
    mkdir -p "/var/jobber/${USERID}" && \
    chown -R jobberuser:jobberuser "/var/jobber/${USERID}"

COPY --chown=jobberuser:jobberuser ./etc/docker/jobber.yaml /home/jobberuser/.jobber
RUN chown jobberuser:jobberuser /home/jobberuser/.jobber && \
    chmod 0600 /home/jobberuser/.jobber
USER jobberuser
ENTRYPOINT ["jobberrunner"]
CMD ["/home/jobberuser/.jobber"]

USER root
RUN apk add --update --no-cache docker su-exec
# TODO : mauvaise pratique sécurité (https://github.com/ncopa/su-exec/issues/2#issuecomment-202873774)
RUN chmod u+s /sbin/su-exec

USER jobberuser